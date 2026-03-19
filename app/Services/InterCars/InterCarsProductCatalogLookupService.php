<?php

namespace App\Services\InterCars;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class InterCarsProductCatalogLookupService
{
    /**
     * @return bool
     */
    public function configured(): bool
    {
        return $this->findProductInformationFile() !== null;
    }


    /**
     * @param Collection $eans
     *
     * @return array
     */
    public function resolveSkusByEans(Collection $eans): array
    {
        $targets = $eans->map(function ($ean) {
            return $this->normalizeEan($ean);
        })
        ->filter()
        ->unique()
        ->values();

        if ($targets->isEmpty()) {
            return [];
        }

        $source = $this->findProductInformationFile();

        if ($source === null) {
            return [];
        }

        if (Str::endsWith(Str::lower($source), '.zip')) {
            return $this->scanZipFile($source, $targets->all());
        }

        return $this->scanCsvFile($source, $targets->all());
    }


    /**
     * @return string|null
     */
    public function sourceDescription(): ?string
    {
        return $this->findProductInformationFile();
    }


    /**
     * @return string|null
     */
    private function findProductInformationFile(): ?string
    {
        $path = trim((string) config('services.intercars.product_information_path', ''));

        if ($path === '') {
            return null;
        }

        if (is_file($path)) {
            return $path;
        }

        if ( ! is_dir($path)) {
            return null;
        }

        $candidates = collect(scandir($path) ?: [])
            ->filter(function ($file) {
                return Str::startsWith($file, 'ProductInformation')
                    && (Str::endsWith(Str::lower($file), '.csv') || Str::endsWith(Str::lower($file), '.zip'));
            })
            ->map(function ($file) use ($path) {
                return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
            })
            ->filter(function ($file) {
                return is_file($file);
            })
            ->sortByDesc(function ($file) {
                return filemtime($file) ?: 0;
            })
            ->values();

        return $candidates->first();
    }


    /**
     * @param string $path
     * @param array  $targets
     *
     * @return array
     */
    private function scanCsvFile(string $path, array $targets): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('Nije moguće otvoriti Inter Cars ProductInformation datoteku: ' . $path);
        }

        try {
            return $this->scanCsvStream($handle, $targets);
        } finally {
            fclose($handle);
        }
    }


    /**
     * @param string $path
     * @param array  $targets
     *
     * @return array
     */
    private function scanZipFile(string $path, array $targets): array
    {
        if ( ! class_exists(\ZipArchive::class)) {
            throw new RuntimeException('PHP ZipArchive ekstenzija nije dostupna, pa nije moguće čitati ProductInformation ZIP.');
        }

        $zip = new \ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException('Nije moguće otvoriti Inter Cars ProductInformation ZIP datoteku: ' . $path);
        }

        try {
            $entryName = null;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);

                if ($name && Str::endsWith(Str::lower($name), '.csv')) {
                    $entryName = $name;
                    break;
                }
            }

            if ($entryName === null) {
                throw new RuntimeException('ProductInformation ZIP ne sadrži CSV datoteku.');
            }

            $stream = $zip->getStream($entryName);

            if ($stream === false) {
                throw new RuntimeException('Nije moguće čitati CSV iz ProductInformation ZIP datoteke.');
            }

            try {
                return $this->scanCsvStream($stream, $targets);
            } finally {
                fclose($stream);
            }
        } finally {
            $zip->close();
        }
    }


    /**
     * @param resource $stream
     * @param array    $targets
     *
     * @return array
     */
    private function scanCsvStream($stream, array $targets): array
    {
        $remaining = array_fill_keys($targets, true);
        $resolved = [];
        $header = fgetcsv($stream, 0, ';');

        if ( ! is_array($header)) {
            return [];
        }

        $headerMap = collect($header)
            ->map(function ($column, $index) {
                return [Str::upper(trim($this->stripBom((string) $column))), $index];
            })
            ->filter(function ($item) {
                return $item[0] !== '';
            })
            ->mapWithKeys(function ($item) {
                return [$item[0] => $item[1]];
            });

        $skuIndex = $headerMap->get('TOW_KOD');
        $barcodesIndex = $headerMap->get('BARCODES');
        $indexIndex = $headerMap->get('IC_INDEX');

        if ($skuIndex === null || $barcodesIndex === null) {
            throw new RuntimeException('ProductInformation CSV nema očekivane stupce TOW_KOD i BARCODES.');
        }

        while (($row = fgetcsv($stream, 0, ';')) !== false) {
            if (empty($remaining) || ! is_array($row)) {
                break;
            }

            $sku = trim((string) ($row[$skuIndex] ?? ''));

            if ($sku === '') {
                continue;
            }

            foreach ($this->extractEans((string) ($row[$barcodesIndex] ?? '')) as $ean) {
                if ( ! isset($remaining[$ean]) || isset($resolved[$ean])) {
                    continue;
                }

                $resolved[$ean] = [
                    'sku'   => Str::upper($sku),
                    'index' => trim((string) ($indexIndex !== null ? ($row[$indexIndex] ?? '') : '')),
                ];

                unset($remaining[$ean]);

                if (empty($remaining)) {
                    break;
                }
            }
        }

        return $resolved;
    }


    /**
     * @param mixed $value
     *
     * @return string|null
     */
    private function normalizeEan($value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits !== '' ? $digits : null;
    }


    /**
     * @param string $value
     *
     * @return array
     */
    private function extractEans(string $value): array
    {
        return collect(preg_split('/\D+/', $value) ?: [])
            ->map(function ($ean) {
                return $this->normalizeEan((string) $ean);
            })
            ->filter(function ($ean) {
                return $ean !== null;
            })
            ->unique()
            ->values()
            ->all();
    }


    /**
     * @param string $value
     *
     * @return string
     */
    private function stripBom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value) ?: $value;
    }
}

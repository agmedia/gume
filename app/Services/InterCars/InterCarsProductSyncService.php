<?php

namespace App\Services\InterCars;

use App\Helpers\Import;
use App\Helpers\ProductHelper;
use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Catalog\Product\ProductAttribute;
use App\Models\Back\Catalog\Product\ProductCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class InterCarsProductSyncService
{
    private const RESULT_LIMIT = 200;
    private const PRODUCT_BATCH_SIZE = 100;

    /**
     * @var InterCarsClient
     */
    private $client;

    /**
     * @var InterCarsProductCatalogLookupService
     */
    private $catalogLookup;

    /**
     * @var Import
     */
    private $import;


    /**
     * @param InterCarsClient                     $client
     * @param InterCarsProductCatalogLookupService $catalogLookup
     */
    public function __construct(InterCarsClient $client, InterCarsProductCatalogLookupService $catalogLookup)
    {
        $this->client = $client;
        $this->catalogLookup = $catalogLookup;
        $this->import = new Import();
    }


    /**
     * @return bool
     */
    public function catalogLookupConfigured(): bool
    {
        return $this->catalogLookup->configured();
    }


    /**
     * @return string|null
     */
    public function catalogLookupSource(): ?string
    {
        return $this->catalogLookup->sourceDescription();
    }


    /**
     * @param string $rawSkus
     * @param array  $options
     *
     * @return array
     */
    public function sync(string $rawSkus, array $options = []): array
    {
        return $this->syncSkus($this->parseSkus($rawSkus), $options);
    }


    /**
     * @param array $options
     *
     * @return array
     */
    public function syncExistingProducts(array $options = []): array
    {
        $products = $this->getLocalProductsForSync($options);
        $prepared = $this->prepareProductsForSync($products);
        $report = $prepared['report'];

        if ($prepared['sku_products']->isNotEmpty()) {
            $report = $this->mergeReports(
                $report,
                $this->syncSkus(
                    $prepared['sku_products']->keys()->values(),
                    array_merge($options, ['create_missing' => false]),
                    $prepared['sku_products']
                )
            );
        }

        if ($prepared['index_products']->isNotEmpty()) {
            $report = $this->mergeReports(
                $report,
                $this->syncIndexes(
                    $prepared['index_products']->keys()->values(),
                    $prepared['index_products'],
                    array_merge($options, ['create_missing' => false])
                )
            );
        }

        return $report;
    }


    /**
     * @param array $options
     *
     * @return array
     */
    public function syncStockStatusForExistingProducts(array $options = []): array
    {
        $report = $this->emptyReport();
        $seenRemoteSkus = [];
        $seenIndexes = [];

        $this->streamLocalProductsForSync($options, function (Collection $products) use (&$report, &$seenRemoteSkus, &$seenIndexes, $options) {
            $prepared = $this->prepareProductsForSync($products, $seenRemoteSkus, $seenIndexes);
            $seenRemoteSkus = $prepared['seen_remote_skus'];
            $seenIndexes = $prepared['seen_indexes'];

            $report = $this->mergeReports($report, $prepared['report']);

            if ($prepared['sku_products']->isNotEmpty()) {
                $report = $this->mergeReports(
                    $report,
                    $this->syncStockBySku($prepared['sku_products'], $options)
                );
            }

            if ($prepared['index_products']->isNotEmpty()) {
                $report = $this->mergeReports(
                    $report,
                    $this->syncStockByIndex($prepared['index_products'], $options)
                );
            }
        });

        return $report;
    }


    /**
     * @param Collection      $skus
     * @param array           $options
     * @param Collection|null $existingProducts
     *
     * @return array
     */
    private function syncSkus(Collection $skus, array $options = [], ?Collection $existingProducts = null): array
    {
        $quotes = $this->getQuoteMap($skus);
        $report = $this->emptyReport($skus->count());

        foreach ($skus as $sku) {
            $product = $existingProducts
                ? $existingProducts->get($sku)
                : Product::query()->whereRaw('UPPER(sku) = ?', [$sku])->first();

            try {
                $catalogProduct = $this->client->getCatalogProductBySku($sku);
                $quoteProduct = $quotes[$sku] ?? null;

                if ( ! $catalogProduct && ! $quoteProduct) {
                    throw new RuntimeException('Artikl nije pronađen na Inter Cars API-ju.');
                }

                if ( ! $product && empty($options['create_missing'])) {
                    $report['skipped']++;
                    $this->pushResult($report, $this->buildResultRow($sku, 'preskočeno', 'Artikl ne postoji lokalno, a kreiranje novih artikala nije uključeno.'));

                    continue;
                }

                if ( ! $product && empty($options['category_id'])) {
                    throw new RuntimeException('Za novi artikl treba odabrati lokalnu kategoriju.');
                }

                if ($product) {
                    $product = $this->storeProduct($product, $catalogProduct, $quoteProduct, $options, false);
                    $report['updated']++;
                    $this->pushResult($report, $this->buildResultRow($sku, 'ažurirano', 'Postojeći artikl je uspješno ažuriran.', $product));
                } else {
                    $product = $this->storeProduct(null, $catalogProduct, $quoteProduct, $options, true);
                    $report['created']++;
                    $this->pushResult($report, $this->buildResultRow($sku, 'kreirano', 'Novi artikl je uspješno importiran.', $product));
                }
            } catch (Throwable $e) {
                Log::warning('Inter Cars sync failed for SKU ' . $sku . ': ' . $e->getMessage());

                $report['failed']++;
                $this->pushResult($report, $this->buildResultRow($sku, 'greška', $e->getMessage(), $product));
            }
        }

        return $report;
    }


    /**
     * @param Collection $indexes
     * @param Collection $existingProducts
     * @param array      $options
     *
     * @return array
     */
    private function syncIndexes(Collection $indexes, Collection $existingProducts, array $options = []): array
    {
        $quotes = $this->getQuoteMapByIndex($indexes);
        $report = $this->emptyReport($indexes->count());

        foreach ($indexes as $index) {
            $product = $existingProducts->get($index);

            try {
                $catalogProduct = $this->client->getCatalogProductByIndex($index);
                $quoteProduct = $quotes[$index] ?? null;

                if ( ! $catalogProduct && ! $quoteProduct) {
                    throw new RuntimeException('Artikl nije pronađen na Inter Cars API-ju po indexu.');
                }

                if ( ! $product) {
                    $report['skipped']++;
                    $this->pushResult($report, $this->buildResultRow($index, 'preskočeno', 'Lokalni artikl nije pronađen za zadani index.'));

                    continue;
                }

                $product = $this->storeProduct($product, $catalogProduct, $quoteProduct, $options, false);
                $report['updated']++;
                $this->pushResult($report, $this->buildResultRow(
                    (string) data_get($catalogProduct, 'sku', data_get($quoteProduct, 'sku', $index)),
                    'ažurirano',
                    'Postojeći artikl je uspješno ažuriran preko IC indexa.',
                    $product
                ));
            } catch (Throwable $e) {
                Log::warning('Inter Cars sync failed for index ' . $index . ': ' . $e->getMessage());

                $report['failed']++;
                $this->pushResult($report, $this->buildResultRow($index, 'greška', $e->getMessage(), $product));
            }
        }

        return $report;
    }


    /**
     * @param Collection $products
     * @param array      $options
     *
     * @return array
     */
    private function syncStockBySku(Collection $products, array $options = []): array
    {
        $quotes = $this->getQuoteMap($products->keys()->values());
        $report = $this->emptyReport($products->count());

        foreach ($products as $sku => $product) {
            $quoteProduct = $quotes[$sku] ?? null;

            if ($quoteProduct === null) {
                $report['skipped']++;
                $this->pushResult(
                    $report,
                    $this->buildResultRow(
                        $sku,
                        'preskočeno',
                        'Artikl nije potvrđen na Inter Cars API-ju. Količina i status nisu mijenjani.',
                        $product
                    )
                );

                continue;
            }

            $this->applyStockUpdate($report, $product, $quoteProduct, $options, (string) data_get($quoteProduct, 'sku', $sku), false);
        }

        return $report;
    }


    /**
     * @param Collection $products
     * @param array      $options
     *
     * @return array
     */
    private function syncStockByIndex(Collection $products, array $options = []): array
    {
        $quotes = $this->getQuoteMapByIndex($products->keys()->values());
        $report = $this->emptyReport($products->count());

        foreach ($products as $index => $product) {
            $quoteProduct = $quotes[$index] ?? null;

            if ($quoteProduct === null) {
                $report['skipped']++;
                $this->pushResult(
                    $report,
                    $this->buildResultRow(
                        $index,
                        'preskočeno',
                        'Artikl nije potvrđen na Inter Cars API-ju po indexu. Količina i status nisu mijenjani.',
                        $product
                    )
                );

                continue;
            }

            $this->applyStockUpdate(
                $report,
                $product,
                $quoteProduct,
                $options,
                (string) data_get($quoteProduct, 'sku', $index),
                true
            );
        }

        return $report;
    }


    /**
     * @param array   $report
     * @param Product $product
     * @param array   $quoteProduct
     * @param array   $options
     * @param string  $remoteSku
     * @param bool    $usedIndex
     *
     * @return void
     */
    private function applyStockUpdate(array &$report, Product $product, array $quoteProduct, array $options, string $remoteSku, bool $usedIndex): void
    {
        $quantity = $this->resolveQuantity($quoteProduct);

        if ($quantity === null) {
            $report['failed']++;
            $this->pushResult($report, $this->buildResultRow($remoteSku, 'greška', 'Inter Cars API nije vratio količinu za artikl.', $product));

            return;
        }

        $payload = [];
        $statusChanged = false;
        $newStatus = (int) $product->status;

        if ((int) $product->quantity !== $quantity) {
            $payload['quantity'] = $quantity;
        }

        if ( ! empty($options['sync_status'])) {
            $newStatus = $quantity > 0 ? 1 : 0;

            if ((int) $product->status !== $newStatus) {
                $payload['status'] = $newStatus;
                $statusChanged = true;
            }
        }

        if (empty($payload)) {
            $report['skipped']++;
            $this->pushResult(
                $report,
                $this->buildResultRow(
                    $remoteSku,
                    'preskočeno',
                    'Količina i status su već usklađeni.',
                    $product
                )
            );

            return;
        }

        $product->update($payload);

        $report['updated']++;

        if ($statusChanged) {
            if ($newStatus === 1) {
                $report['activated']++;
            } else {
                $report['deactivated']++;
            }
        }

        $syncStatus = ! empty($options['sync_status']);

        if ($usedIndex) {
            $message = $syncStatus
                ? 'Količina i status su uspješno ažurirani preko IC indexa.'
                : 'Količina je uspješno ažurirana preko IC indexa.';
        } else {
            $message = $syncStatus
                ? 'Količina i status su uspješno ažurirani.'
                : 'Količina je uspješno ažurirana.';
        }

        $this->pushResult($report, $this->buildResultRow($remoteSku, 'ažurirano', $message, $product->fresh()));
    }


    /**
     * @param Product|null $product
     * @param array|null   $catalogProduct
     * @param array|null   $quoteProduct
     * @param array        $options
     * @param bool         $isNew
     *
     * @return Product
     */
    private function storeProduct(?Product $product, ?array $catalogProduct, ?array $quoteProduct, array $options, bool $isNew): Product
    {
        $name = $this->resolveName($catalogProduct, $quoteProduct, $product);
        $description = $this->resolveDescription($catalogProduct, $quoteProduct, $product);
        $brandId = $this->resolveBrandId($catalogProduct, $product);
        $price = $this->resolvePrice($quoteProduct);
        $quantity = $this->resolveQuantity($quoteProduct);
        $ean = $this->resolveEan($catalogProduct, $quoteProduct, $product);

        if ($isNew) {
            $product = Product::query()->create([
                'brand_id'         => $brandId,
                'action_id'        => 0,
                'sku'              => (string) data_get($catalogProduct, 'sku', data_get($quoteProduct, 'sku')),
                'ean'              => $ean,
                'name'             => $name,
                'description'      => $description,
                'slug'             => Str::slug($name . '-' . data_get($catalogProduct, 'sku', data_get($quoteProduct, 'sku'))),
                'url'              => '',
                'category_string'  => '',
                'price'            => $price ?? 0,
                'quantity'         => $quantity ?? 0,
                'tax_id'           => config('settings.default_tax_id', 1),
                'meta_title'       => $name,
                'meta_description' => Str::limit(strip_tags($description ?: $name), 250, ''),
                'status'           => ! empty($options['status']) ? 1 : 0,
            ]);

            ProductCategory::storeData([(int) $options['category_id']], $product->id);
        } else {
            $payload = [
                'brand_id'         => $brandId ?: $product->brand_id,
                'ean'              => $ean ?: $product->ean,
                'name'             => $name,
                'description'      => $description,
                'meta_title'       => $name,
                'meta_description' => Str::limit(strip_tags($description ?: $name), 250, ''),
            ];

            if ($price !== null) {
                $payload['price'] = $price;
            }

            if ($quantity !== null) {
                $payload['quantity'] = $quantity;
            }

            $product->update($payload);

            if ( ! $product->category() && ! empty($options['category_id'])) {
                ProductCategory::storeData([(int) $options['category_id']], $product->id);
            }
        }

        $product = $product->fresh();
        $product->update([
            'url'             => ProductHelper::url($product),
            'category_string' => ProductHelper::categoryString($product),
        ]);

        $this->syncAttributes($product, $catalogProduct, $quoteProduct);

        return $product->fresh();
    }


    /**
     * @param array|null   $catalogProduct
     * @param array|null   $quoteProduct
     * @param Product|null $product
     *
     * @return string
     */
    private function resolveName(?array $catalogProduct, ?array $quoteProduct, ?Product $product): string
    {
        $name = data_get($quoteProduct, 'name')
            ?: data_get($catalogProduct, 'shortDescription')
            ?: data_get($catalogProduct, 'description')
            ?: ($product ? $product->name : '');

        return trim($name) ?: 'Inter Cars artikl';
    }


    /**
     * @param array|null   $catalogProduct
     * @param array|null   $quoteProduct
     * @param Product|null $product
     *
     * @return string
     */
    private function resolveDescription(?array $catalogProduct, ?array $quoteProduct, ?Product $product): string
    {
        return trim(
            (string) (
                data_get($catalogProduct, 'description')
                ?: data_get($quoteProduct, 'description')
                ?: ($product ? $product->description : '')
            )
        );
    }


    /**
     * @param array|null   $catalogProduct
     * @param Product|null $product
     *
     * @return int
     */
    private function resolveBrandId(?array $catalogProduct, ?Product $product): int
    {
        $brand = trim((string) data_get($catalogProduct, 'brand'));

        if ($brand !== '') {
            return $this->import->resolveBrand($brand);
        }

        if ($product && $product->brand_id) {
            return (int) $product->brand_id;
        }

        return config('settings.unknown_brand', 0);
    }


    /**
     * @param array|null $quoteProduct
     *
     * @return float|null
     */
    private function resolvePrice(?array $quoteProduct): ?float
    {
        $price = data_get($quoteProduct, 'price.customerPriceGross');

        if ($price === null) {
            $price = data_get($quoteProduct, 'price.listPriceGross');
        }

        return $this->normalizeDecimal($price);
    }


    /**
     * @param array|null $quoteProduct
     *
     * @return int|null
     */
    private function resolveQuantity(?array $quoteProduct): ?int
    {
        $lines = collect(data_get($quoteProduct, 'lines', []));

        if ($lines->isEmpty()) {
            return null;
        }

        return (int) $lines->sum(function ($line) {
            return (int) data_get($line, 'availability', 0);
        });
    }


    /**
     * @param array|null   $catalogProduct
     * @param array|null   $quoteProduct
     * @param Product|null $product
     *
     * @return string|null
     */
    private function resolveEan(?array $catalogProduct, ?array $quoteProduct, ?Product $product): ?string
    {
        $eans = data_get($catalogProduct, 'eans', data_get($quoteProduct, 'eans', []));

        if (is_array($eans) && isset($eans[0])) {
            return Str::limit((string) $eans[0], 14, '');
        }

        if ($product && $product->ean) {
            return $product->ean;
        }

        return null;
    }


    /**
     * @param Product     $product
     * @param array|null  $catalogProduct
     * @param array|null  $quoteProduct
     *
     * @return void
     */
    private function syncAttributes(Product $product, ?array $catalogProduct, ?array $quoteProduct): void
    {
        $attributes = [
            'IC Izvor'            => 'Inter Cars API',
            'IC Index'            => data_get($catalogProduct, 'index', data_get($quoteProduct, 'index')),
            'IC TecDoc'           => data_get($catalogProduct, 'tecDoc'),
            'IC TecDoc Prod'      => data_get($catalogProduct, 'tecDocProd'),
            'IC Broj Artikla'     => data_get($catalogProduct, 'articleNumber'),
            'IC Carinska Tarifa'  => data_get($catalogProduct, 'customsCode'),
            'IC GTU'              => data_get($catalogProduct, 'gtuCode', data_get($quoteProduct, 'gtuCode')),
            'IC Povrat Blokiran'  => $this->resolveBooleanValue(data_get($catalogProduct, 'blockedReturn', data_get($quoteProduct, 'blockedReturn'))),
            'IC EAN Lista'        => $this->resolveEansList(data_get($catalogProduct, 'eans', data_get($quoteProduct, 'eans', []))),
        ];

        foreach ($attributes as $title => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $attributeId = $this->import->resolveAttribute($title);
            $storedValue = Str::limit((string) $value, 255, '');
            $attribute = ProductAttribute::query()
                                         ->where('product_id', $product->id)
                                         ->where('attribute_id', $attributeId)
                                         ->first();

            if ($attribute) {
                $attribute->update(['value' => $storedValue]);
            } else {
                ProductAttribute::query()->create([
                    'product_id'   => $product->id,
                    'attribute_id' => $attributeId,
                    'value'        => $storedValue,
                ]);
            }
        }
    }


    /**
     * @param string $rawSkus
     *
     * @return Collection
     */
    private function parseSkus(string $rawSkus): Collection
    {
        $items = preg_split('/[\s,;]+/', Str::upper($rawSkus)) ?: [];

        return collect($items)
            ->map(function ($sku) {
                return trim((string) $sku);
            })
            ->filter()
            ->unique()
            ->values();
    }


    /**
     * @param array $options
     *
     * @return Collection
     */
    private function getLocalProductsForSync(array $options = []): Collection
    {
        return $this->getLocalProductsForSyncQuery($options)
                    ->orderBy('products.name')
                    ->get();
    }


    /**
     * @param Collection $products
     *
     * @return array
     */
    private function prepareProductsForSync(Collection $products, array $seenRemoteSkus = [], array $seenIndexes = []): array
    {
        $catalogLookupReady = $this->catalogLookup->configured();
        $eanLookup = $this->catalogLookup->resolveSkusByEans($products->pluck('ean'));
        $resolvedSkuProducts = collect();
        $resolvedIndexProducts = collect();
        $report = $this->emptyReport();

        foreach ($products as $product) {
            $ean = $this->normalizeEan((string) $product->ean);
            $localSku = trim((string) $product->sku);

            if ($ean && isset($eanLookup[$ean]['sku'])) {
                $remoteSku = Str::upper((string) $eanLookup[$ean]['sku']);

                if (isset($seenRemoteSkus[$remoteSku]) || $resolvedSkuProducts->has($remoteSku)) {
                    $report['total']++;
                    $report['skipped']++;
                    $this->pushResult($report, $this->buildResultRow(
                        $remoteSku,
                        'preskočeno',
                        'Više lokalnih artikala mapirano je na isti Inter Cars SKU. Sinkroniziran je samo prvi.',
                        $product
                    ));

                    continue;
                }

                $resolvedSkuProducts->put($remoteSku, $product);
                $seenRemoteSkus[$remoteSku] = true;

                continue;
            }

            if ($localSku === '') {
                $report['total']++;
                $report['skipped']++;
                $this->pushResult($report, $this->buildResultRow(
                    '',
                    'preskočeno',
                    $ean
                        ? ($catalogLookupReady
                            ? 'EAN artikla nije pronađen u Inter Cars ProductInformation katalogu.'
                            : 'EAN lookup nije dostupan jer ProductInformation katalog nije konfiguriran.')
                        : 'Artikl nema EAN ni SKU za sinkronizaciju.',
                    $product
                ));

                continue;
            }

            if (isset($seenIndexes[$localSku]) || $resolvedIndexProducts->has($localSku)) {
                $report['total']++;
                $report['skipped']++;
                $this->pushResult($report, $this->buildResultRow(
                    $localSku,
                    'preskočeno',
                    'Više lokalnih artikala ima isti lokalni SKU / product_code. Sinkroniziran je samo prvi.',
                    $product
                ));

                continue;
            }

            $resolvedIndexProducts->put($localSku, $product);
            $seenIndexes[$localSku] = true;
        }

        return [
            'sku_products'      => $resolvedSkuProducts,
            'index_products'    => $resolvedIndexProducts,
            'report'            => $report,
            'seen_remote_skus'  => $seenRemoteSkus,
            'seen_indexes'      => $seenIndexes,
        ];
    }


    /**
     * @param array    $options
     * @param callable $callback
     *
     * @return void
     */
    private function streamLocalProductsForSync(array $options, callable $callback): void
    {
        $baseQuery = $this->getLocalProductsForSyncQuery($options)->orderBy('products.id');
        $lastId = 0;

        while (true) {
            $chunk = (clone $baseQuery)
                ->where('products.id', '>', $lastId)
                ->limit(self::PRODUCT_BATCH_SIZE)
                ->get();

            if ($chunk->isEmpty()) {
                return;
            }

            $callback($chunk);
            $lastId = (int) $chunk->last()->id;
        }
    }


    /**
     * @param array $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getLocalProductsForSyncQuery(array $options = [])
    {
        $query = Product::query()
                        ->select('products.*')
                        ->where(function ($builder) {
                            $builder->where(function ($subQuery) {
                                $subQuery->whereNotNull('products.ean')
                                         ->where('products.ean', '!=', '');
                            })->orWhere(function ($subQuery) {
                                $subQuery->whereNotNull('products.sku')
                                         ->where('products.sku', '!=', '');
                            });
                        });

        if ( ! empty($options['product_ids'])) {
            $query->whereIn('products.id', collect($options['product_ids'])->map(function ($id) {
                return (int) $id;
            })->filter()->values()->all());
        }

        if ( ! empty($options['category_id'])) {
            $query->join('product_category as pc', 'pc.product_id', '=', 'products.id')
                  ->where('pc.category_id', (int) $options['category_id'])
                  ->distinct();
        }

        if ( ! empty($options['only_active'])) {
            $query->where('products.status', 1);
        }

        return $query;
    }


    /**
     * @param Collection $skus
     *
     * @return array
     */
    private function getQuoteMap(Collection $skus): array
    {
        $map = [];

        foreach ($skus->chunk(25) as $chunk) {
            try {
                $response = $this->client->quote(
                    $chunk->map(function ($sku) {
                        return [
                            'sku'      => $sku,
                            'quantity' => 1,
                        ];
                    })->values()->all()
                );

                foreach ($response as $item) {
                    $sku = (string) data_get($item, 'sku');

                    if ($sku !== '') {
                        $map[$sku] = $item;
                    }
                }
            } catch (Throwable $e) {
                foreach ($chunk as $sku) {
                    try {
                        $response = $this->client->quote([[
                            'sku'      => $sku,
                            'quantity' => 1,
                        ]]);

                        if (isset($response[0]) && data_get($response[0], 'sku')) {
                            $map[(string) data_get($response[0], 'sku')] = $response[0];
                        }
                    } catch (Throwable $singleError) {
                        Log::warning('Inter Cars quote failed for SKU ' . $sku . ': ' . $singleError->getMessage());
                    }
                }
            }
        }

        return $map;
    }


    /**
     * @param Collection $indexes
     *
     * @return array
     */
    private function getQuoteMapByIndex(Collection $indexes): array
    {
        $map = [];

        foreach ($indexes->chunk(25) as $chunk) {
            try {
                $response = $this->client->quoteByIndex(
                    $chunk->map(function ($index) {
                        return [
                            'index'    => $index,
                            'quantity' => 1,
                        ];
                    })->values()->all()
                );

                foreach ($response as $item) {
                    $index = trim((string) data_get($item, 'index'));

                    if ($index !== '') {
                        $map[$index] = $item;
                    }
                }
            } catch (Throwable $e) {
                foreach ($chunk as $index) {
                    try {
                        $response = $this->client->quoteByIndex([[
                            'index'    => $index,
                            'quantity' => 1,
                        ]]);

                        if (isset($response[0])) {
                            $responseIndex = trim((string) data_get($response[0], 'index', $index));

                            if ($responseIndex !== '') {
                                $map[$responseIndex] = $response[0];
                            }
                        }
                    } catch (Throwable $singleError) {
                        Log::warning('Inter Cars quote failed for index ' . $index . ': ' . $singleError->getMessage());
                    }
                }
            }
        }

        return $map;
    }


    /**
     * @param int $total
     *
     * @return array
     */
    private function emptyReport(int $total = 0): array
    {
        return [
            'total'             => $total,
            'created'           => 0,
            'updated'           => 0,
            'skipped'           => 0,
            'failed'            => 0,
            'activated'         => 0,
            'deactivated'       => 0,
            'results'           => [],
            'results_truncated' => false,
        ];
    }


    /**
     * @param array $first
     * @param array $second
     *
     * @return array
     */
    private function mergeReports(array $first, array $second): array
    {
        $results = array_merge($first['results'] ?? [], $second['results'] ?? []);

        if (count($results) > self::RESULT_LIMIT) {
            $results = array_slice($results, 0, self::RESULT_LIMIT);
        }

        return [
            'total'             => ($first['total'] ?? 0) + ($second['total'] ?? 0),
            'created'           => ($first['created'] ?? 0) + ($second['created'] ?? 0),
            'updated'           => ($first['updated'] ?? 0) + ($second['updated'] ?? 0),
            'skipped'           => ($first['skipped'] ?? 0) + ($second['skipped'] ?? 0),
            'failed'            => ($first['failed'] ?? 0) + ($second['failed'] ?? 0),
            'activated'         => ($first['activated'] ?? 0) + ($second['activated'] ?? 0),
            'deactivated'       => ($first['deactivated'] ?? 0) + ($second['deactivated'] ?? 0),
            'results'           => $results,
            'results_truncated' => ($first['results_truncated'] ?? false)
                || ($second['results_truncated'] ?? false)
                || count(array_merge($first['results'] ?? [], $second['results'] ?? [])) > self::RESULT_LIMIT,
        ];
    }


    /**
     * @param array $report
     * @param array $row
     *
     * @return void
     */
    private function pushResult(array &$report, array $row): void
    {
        if (count($report['results']) >= self::RESULT_LIMIT) {
            $report['results_truncated'] = true;

            return;
        }

        $report['results'][] = $row;
    }


    /**
     * @param mixed $value
     *
     * @return float|null
     */
    private function normalizeDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace([' ', ','], ['', '.'], (string) $value);

        return is_numeric($normalized) ? (float) $normalized : null;
    }


    /**
     * @param mixed $value
     *
     * @return string|null
     */
    private function resolveBooleanValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $value ? 'Da' : 'Ne';
    }


    /**
     * @param mixed $eans
     *
     * @return string|null
     */
    private function resolveEansList($eans): ?string
    {
        if ( ! is_array($eans) || empty($eans)) {
            return null;
        }

        return implode(', ', array_filter($eans));
    }


    /**
     * @param string $value
     *
     * @return string|null
     */
    private function normalizeEan(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value);

        return $digits !== '' ? $digits : null;
    }


    /**
     * @param string      $sku
     * @param string      $status
     * @param string      $message
     * @param Product|null $product
     *
     * @return array
     */
    private function buildResultRow(string $sku, string $status, string $message, ?Product $product = null): array
    {
        return [
            'sku'        => $sku,
            'local_sku'  => $product ? (string) $product->sku : '',
            'ean'        => $product ? (string) $product->ean : '',
            'status'     => $status,
            'message'    => $message,
            'name'       => $product ? $product->name : '',
            'product_id' => $product ? $product->id : null,
        ];
    }
}

<?php

namespace App\Models\Back\Settings\Api;

use App\Helpers\ImageHelper;
use App\Helpers\Import;
use App\Helpers\ProductHelper;
use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Catalog\Product\ProductAttribute;
use App\Models\Back\Catalog\Product\ProductCategory;
use App\Models\Back\Catalog\Product\ProductImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;
use XMLReader;

class DataFeedWatch
{
    private const DEFAULT_FEED_URLS = [
        'https://feeds.datafeedwatch.com/70335/d2bfb7399e3bee04d0dabb9b5f0954de960f8569.xml',
        'https://feeds.datafeedwatch.com/70335/d8aa73ceb924b75fd493399154b0c61f3ec93178.xml',
    ];

    /**
     * @param bool $secondFeeds
     *
     * @return int
     */
    public function updatePricesAndQuantity(bool $secondFeeds = true): int
    {
        $report = $this->syncProducts([
            'import_missing'        => false,
            'include_secondary_feed'=> $secondFeeds,
        ]);

        return $report['updated'];
    }


    /**
     * @param bool $secondFeeds
     *
     * @return array
     */
    public function importAndUpdate(bool $secondFeeds = true): array
    {
        return $this->syncProducts([
            'import_missing'         => true,
            'include_secondary_feed' => $secondFeeds,
        ]);
    }


    /**
     * @param array $options
     *
     * @return array
     */
    public function syncProducts(array $options = []): array
    {
        $options = array_merge([
            'import_missing'         => false,
            'include_secondary_feed' => true,
            'sync_images'            => true,
        ], $options);

        $import = new Import();
        $report = [
            'total'   => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed'  => 0,
        ];
        $processed = [];

        foreach ($this->resolveFeedUrls((bool) $options['include_secondary_feed']) as $feedUrl) {
            try {
                $this->scanFeed($feedUrl, function (SimpleXMLElement $item) use (&$report, &$processed, $import, $options) {
                    $data = $this->mapItem($item);
                    $categoryIds = $this->resolveCategoryIds($data, $import);

                    if ($data['sku'] === '') {
                        $report['skipped']++;

                        return;
                    }

                    if (empty($categoryIds)) {
                        Log::info('DataFeedWatch skipped SKU because no existing category match was found.', [
                            'sku'       => $data['sku'],
                            'feed_name' => $data['name'],
                            'category'  => $data['category'],
                            'tyre_type' => $data['tyre_type'],
                        ]);

                        $report['skipped']++;

                        return;
                    }

                    $key = Str::upper($data['sku']);

                    if (isset($processed[$key])) {
                        $report['skipped']++;

                        return;
                    }

                    $processed[$key] = true;
                    $report['total']++;

                    try {
                        $product = $this->findExistingProduct($data);

                        if ($product) {
                            $this->updateProduct($product, $data, $categoryIds, $import, $options);
                            $report['updated']++;

                            return;
                        }

                        if ( ! $options['import_missing']) {
                            $report['skipped']++;

                            return;
                        }

                        $this->createProduct($data, $categoryIds, $import, $options);
                        $report['created']++;
                    } catch (Throwable $e) {
                        Log::warning('DataFeedWatch sync failed for SKU ' . $data['sku'] . ': ' . $e->getMessage(), [
                            'exception' => $e,
                        ]);

                        $report['failed']++;
                    }
                });
            } catch (Throwable $e) {
                Log::warning('DataFeedWatch feed scan failed: ' . $e->getMessage(), [
                    'feed_url'  => $feedUrl,
                    'exception' => $e,
                ]);

                $report['failed']++;
            }
        }

        return $report;
    }


    /**
     * @param bool $includeSecondaryFeed
     *
     * @return array
     */
    private function resolveFeedUrls(bool $includeSecondaryFeed): array
    {
        if ($includeSecondaryFeed) {
            return self::DEFAULT_FEED_URLS;
        }

        return [self::DEFAULT_FEED_URLS[0]];
    }


    /**
     * @param string   $feedUrl
     * @param callable $callback
     *
     * @return void
     */
    private function scanFeed(string $feedUrl, callable $callback): void
    {
        $reader = new XMLReader();
        $previousUseInternalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        if ( ! $reader->open($feedUrl)) {
            throw new \RuntimeException('Nije moguće otvoriti DataFeedWatch feed: ' . $feedUrl);
        }

        try {
            while ($reader->read()) {
                if ($reader->nodeType !== XMLReader::ELEMENT || $reader->name !== 'product') {
                    continue;
                }

                $itemXml = $reader->readOuterXml();

                if ( ! is_string($itemXml) || trim($itemXml) === '') {
                    Log::warning('DataFeedWatch skipped empty product node while scanning feed.', [
                        'feed_url' => $feedUrl,
                    ]);

                    libxml_clear_errors();

                    continue;
                }

                try {
                    $callback(new SimpleXMLElement($itemXml));
                } catch (Throwable $e) {
                    $errors = array_values(array_filter(array_map(static function (\LibXMLError $error) {
                        return trim($error->message);
                    }, libxml_get_errors())));

                    Log::warning('DataFeedWatch skipped malformed product node while scanning feed.', [
                        'feed_url'      => $feedUrl,
                        'message'       => $e->getMessage(),
                        'libxml_errors' => $errors,
                    ]);
                } finally {
                    libxml_clear_errors();
                }
            }
        } finally {
            $reader->close();
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseInternalErrors);
        }
    }


    /**
     * @param SimpleXMLElement $item
     *
     * @return array
     */
    private function mapItem(SimpleXMLElement $item): array
    {
        $productCategory = trim((string) $item->category);
        $tyreType = trim((string) $item->tyre_type);
        $description = trim((string) $item->description);
        $name = trim((string) $item->product_name);

        return [
            'sku'               => trim((string) $item->product_code),
            'ean'               => $this->normalizeEan((string) $item->ean),
            'name'              => $name,
            'description'       => $description,
            'price'             => $this->normalizeDecimal((string) $item->price),
            'quantity'          => $this->normalizeInteger((string) $item->stock_number),
            'status'            => $this->resolveStatus((string) $item->stock_number, (string) $item->warehouse_condition),
            'brand'             => trim((string) $item->brand),
            'category'          => $productCategory,
            'tyre_type'         => $tyreType,
            'season'            => $this->resolveSeason($productCategory, $tyreType),
            'nosivost'          => trim((string) $item->carrying_capacity_index),
            'promjer'           => trim((string) $item->dimensions_diameter),
            'sirina'            => trim((string) $item->dimensions_width),
            'visina'            => trim((string) $item->dimensions_height),
            'buka'              => trim((string) $item->external_rolling_noise),
            'prijanjanje'       => trim((string) $item->grip_on_wet_roads),
            'iskoristivost'     => trim((string) $item->fuel_efficiency),
            'main_image'        => trim((string) $item->URL_to_product_image),
            'additional_images' => array_values(array_filter([
                trim((string) $item->URL_additional_product_picture),
                trim((string) $item->URL_additional_product_picture_1),
                trim((string) $item->URL_additional_product_picture_2),
                trim((string) $item->URL_additional_product_picture_3),
                trim((string) $item->URL_additional_product_picture_4),
                trim((string) $item->URL_additional_product_picture_5),
                trim((string) $item->URL_additional_product_picture_6),
            ])),
            'attributes'        => array_filter([
                'Index Brzine' => trim((string) $item->speed_index),
                'EPREL Link'   => trim((string) $item->EPREL_url),
                'MNP'          => trim((string) $item->MNP),
            ]),
        ];
    }


    /**
     * @param array $data
     *
     * @return Product|null
     */
    private function findExistingProduct(array $data): ?Product
    {
        $product = Product::query()->where('sku', $data['sku'])->first();

        if ($product) {
            return $product;
        }

        if ($data['ean']) {
            return Product::query()->where('ean', $data['ean'])->first();
        }

        return null;
    }


    /**
     * @param array  $data
     * @param array  $categoryIds
     * @param Import $import
     * @param array  $options
     *
     * @return Product
     */
    private function createProduct(array $data, array $categoryIds, Import $import, array $options): Product
    {
        $product = Product::query()->create([
            'brand_id'         => $import->resolveBrand($data['brand']),
            'action_id'        => 0,
            'sku'              => $data['sku'],
            'ean'              => $data['ean'],
            'name'             => $data['name'] ?: $data['sku'],
            'description'      => $data['description'],
            'slug'             => Str::slug(($data['name'] ?: $data['sku']) . '-' . $data['sku']),
            'url'              => '',
            'category_string'  => '',
            'price'            => $data['price'] ?? 0,
            'quantity'         => $data['quantity'],
            'tax_id'           => config('settings.default_tax_id', 1),
            'meta_title'       => $data['name'] ?: $data['sku'],
            'meta_description' => Str::limit(strip_tags($data['description'] ?: $data['name']), 250, ''),
            'nosivost'         => $data['nosivost'],
            'namjena'          => $data['tyre_type'],
            'promjer'          => $data['promjer'],
            'sirina'           => $data['sirina'],
            'visina'           => $data['visina'],
            'buka'             => $data['buka'],
            'prijanjanje'      => $data['prijanjanje'],
            'iskoristivost'    => $data['iskoristivost'],
            'sezona'           => $data['season'],
            'status'           => $data['status'],
        ]);

        if ( ! empty($categoryIds)) {
            ProductCategory::storeData($categoryIds, $product->id);
        }

        $this->syncMedia($product, $data, $options, true);
        $this->syncAttributes($product, $data, $import);
        $this->refreshProductMetadata($product);

        return $product->fresh();
    }


    /**
     * @param Product $product
     * @param array   $data
     * @param array   $categoryIds
     * @param Import  $import
     * @param array   $options
     *
     * @return void
     */
    private function updateProduct(Product $product, array $data, array $categoryIds, Import $import, array $options): void
    {
        $payload = [
            'price'            => $data['price'] ?? $product->price,
            'quantity'         => $data['quantity'],
            'status'           => $data['status'],
            'ean'              => $data['ean'] ?: $product->ean,
            'name'             => $data['name'] ?: $product->name,
            'description'      => $data['description'] ?: $product->description,
            'meta_title'       => $data['name'] ?: $product->meta_title,
            'meta_description' => Str::limit(strip_tags($data['description'] ?: $data['name'] ?: $product->name), 250, ''),
            'nosivost'         => $data['nosivost'] ?: $product->nosivost,
            'namjena'          => $data['tyre_type'] ?: $product->namjena,
            'promjer'          => $data['promjer'] ?: $product->promjer,
            'sirina'           => $data['sirina'] ?: $product->sirina,
            'visina'           => $data['visina'] ?: $product->visina,
            'buka'             => $data['buka'] ?: $product->buka,
            'prijanjanje'      => $data['prijanjanje'] ?: $product->prijanjanje,
            'iskoristivost'    => $data['iskoristivost'] ?: $product->iskoristivost,
            'sezona'           => $data['season'] ?: $product->sezona,
        ];

        if ($data['brand'] !== '') {
            $payload['brand_id'] = $import->resolveBrand($data['brand']);
        }

        $product->update($payload);

        if ( ! $product->category()) {
            if ( ! empty($categoryIds)) {
                ProductCategory::storeData($categoryIds, $product->id);
            }
        }

        $this->syncMedia($product, $data, $options, false);
        $this->syncAttributes($product, $data, $import);
        $this->refreshProductMetadata($product);
    }


    /**
     * @param Product $product
     * @param array   $data
     * @param array   $options
     * @param bool    $isNew
     *
     * @return void
     */
    private function syncMedia(Product $product, array $data, array $options, bool $isNew): void
    {
        if (empty($options['sync_images'])) {
            return;
        }

        if ($data['main_image'] !== '' && ($isNew || empty($product->image))) {
            try {
                $image = ImageHelper::save($data['main_image'], $data['name'] ?: $data['sku'], $product->id);
                $product->update(['image' => $image]);
            } catch (Throwable $e) {
                Log::warning('DataFeedWatch main image sync failed for SKU ' . $data['sku'] . ': ' . $e->getMessage());
            }
        }

        if (empty($data['additional_images'])) {
            return;
        }

        $hasGallery = ProductImage::query()->where('product_id', $product->id)->exists();

        if ($hasGallery && ! $isNew) {
            return;
        }

        $images = collect($data['additional_images'])
            ->filter()
            ->unique()
            ->values();

        foreach ($images as $sortOrder => $url) {
            if ($url === $data['main_image']) {
                continue;
            }

            try {
                $image = ImageHelper::save($url, ($data['name'] ?: $data['sku']) . '-' . ($sortOrder + 1), $product->id);

                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image'      => $image,
                    'alt'        => $data['name'] ?: $data['sku'],
                    'published'  => 1,
                    'sort_order' => $sortOrder + 1,
                ]);
            } catch (Throwable $e) {
                Log::warning('DataFeedWatch gallery image sync failed for SKU ' . $data['sku'] . ': ' . $e->getMessage());
            }
        }
    }


    /**
     * @param Product $product
     * @param array   $data
     * @param Import  $import
     *
     * @return void
     */
    private function syncAttributes(Product $product, array $data, Import $import): void
    {
        foreach ($data['attributes'] as $title => $value) {
            if ($value === '') {
                continue;
            }

            $attributeId = $import->resolveAttribute($title);
            $attribute = ProductAttribute::query()
                                         ->where('product_id', $product->id)
                                         ->where('attribute_id', $attributeId)
                                         ->first();

            if ($attribute) {
                $attribute->update(['value' => $value]);
            } else {
                ProductAttribute::query()->create([
                    'product_id'   => $product->id,
                    'attribute_id' => $attributeId,
                    'value'        => $value,
                ]);
            }
        }
    }


    /**
     * @param Product $product
     *
     * @return void
     */
    private function refreshProductMetadata(Product $product): void
    {
        $product = $product->fresh();

        $product->update([
            'url'             => ProductHelper::url($product),
            'category_string' => ProductHelper::categoryString($product),
        ]);
    }


    /**
     * @param array  $data
     * @param Import $import
     *
     * @return array
     */
    private function resolveCategoryIds(array $data, Import $import): array
    {
        $categories = [];

        if ($data['tyre_type'] !== '' && $data['category'] !== '' && $data['tyre_type'] !== $data['category']) {
            $categories[] = $data['tyre_type'] . ' > ' . $data['category'];
        }

        if ($data['category'] !== '') {
            $categories[] = $data['category'];
        }

        if ($data['tyre_type'] !== '') {
            $categories[] = $data['tyre_type'];
        }

        return $import->resolveExistingCategories($categories);
    }


    /**
     * @param string $value
     *
     * @return float|null
     */
    private function normalizeDecimal(string $value): ?float
    {
        $value = trim(str_replace('EUR', '', $value));

        if ($value === '') {
            return null;
        }

        $normalized = str_replace([' ', ','], ['', '.'], $value);

        return is_numeric($normalized) ? (float) $normalized : null;
    }


    /**
     * @param string $value
     *
     * @return int
     */
    private function normalizeInteger(string $value): int
    {
        return max(0, (int) preg_replace('/[^\d\-]+/', '', $value));
    }


    /**
     * @param string $stockNumber
     * @param string $warehouseCondition
     *
     * @return int
     */
    private function resolveStatus(string $stockNumber, string $warehouseCondition): int
    {
        $quantity = $this->normalizeInteger($stockNumber);

        if ($quantity > 0) {
            return 1;
        }

        return in_array(Str::lower(trim($warehouseCondition)), ['in_stock', 'available'], true) ? 1 : 0;
    }


    /**
     * @param string $category
     * @param string $tyreType
     *
     * @return string
     */
    private function resolveSeason(string $category, string $tyreType): string
    {
        $haystack = Str::lower($category . ' ' . $tyreType);

        if (Str::contains($haystack, 'zim')) {
            return 'Zima';
        }

        if (Str::contains($haystack, 'ljet')) {
            return 'Ljeto';
        }

        if (Str::contains($haystack, ['cjelogodi', 'all season', '4 godi'])) {
            return 'Cjelogodišnja';
        }

        return '';
    }


    /**
     * @param string|null $value
     *
     * @return string|null
     */
    private function normalizeEan(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits !== '' ? $digits : null;
    }
}

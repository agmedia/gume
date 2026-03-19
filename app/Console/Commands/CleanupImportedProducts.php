<?php

namespace App\Console\Commands;

use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Catalog\Product\ProductAttribute;
use App\Models\Back\Catalog\Product\ProductCategory;
use App\Models\Back\Catalog\Product\ProductImage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupImportedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datafeedwatch:cleanup-imported
                            {--from= : Delete products created on or after this datetime}
                            {--to= : Delete products created on or before this datetime}
                            {--dry-run : Only show candidates}
                            {--uncategorized : Only target products without category links}
                            {--with-orders : Include products that appear in order_products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preview or delete products imported in a given time window.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $query = Product::query()
                        ->select(['id', 'sku', 'name', 'created_at']);

        if ($this->option('from')) {
            $query->where('created_at', '>=', Carbon::parse((string) $this->option('from')));
        }

        if ($this->option('to')) {
            $query->where('created_at', '<=', Carbon::parse((string) $this->option('to')));
        }

        if ($this->option('uncategorized')) {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                         ->from('product_category')
                         ->whereColumn('product_category.product_id', 'products.id');
            });
        }

        if ( ! $this->option('with-orders')) {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                         ->from('order_products')
                         ->whereColumn('order_products.product_id', 'products.id');
            });
        }

        $products = $query->orderBy('created_at')->get();

        if ($products->isEmpty()) {
            $this->info('Nema proizvoda za zadani filter.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'SKU', 'Naziv', 'Created'],
            $products->map(function (Product $product) {
                return [
                    'id'         => $product->id,
                    'sku'        => $product->sku,
                    'name'       => $product->name,
                    'created_at' => optional($product->created_at)->toDateTimeString(),
                ];
            })->all()
        );

        if ($this->option('dry-run')) {
            $this->info('Dry run: pronađeno ' . $products->count() . ' proizvoda.');

            return self::SUCCESS;
        }

        foreach ($products as $product) {
            ProductImage::query()->where('product_id', $product->id)->delete();
            ProductCategory::query()->where('product_id', $product->id)->delete();
            ProductAttribute::query()->where('product_id', $product->id)->delete();
            Storage::disk('products')->deleteDirectory((string) $product->id);
            Product::query()->where('id', $product->id)->delete();
        }

        $this->info('Obrisano ' . $products->count() . ' proizvoda.');

        return self::SUCCESS;
    }
}

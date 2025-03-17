<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Helpers\ImageHelper;
use App\Helpers\ProductHelper;
use App\Models\Front\Catalog\Category;
use App\Models\Back\Catalog\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsPlusSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create settings
        //DB::statement(Storage::get(base_path('/database/seeders/settings.txt')));

        $items = file_get_contents(base_path('/database/seeders/brands.json'));

        if ($items) {
            $items = json_decode($items, true);

            foreach ($items as $item) {
                DB::table('brands')->insert($item);
            }
        }

        //
        $items = file_get_contents(base_path('/database/seeders/attributes.json'));

        if ($items) {
            $items = json_decode($items, true);

            foreach ($items as $item) {
                DB::table('attributes')->insert($item);
            }
        }

        //
        $items = file_get_contents(base_path('/database/seeders/categories.json'));

        if ($items) {
            $items = json_decode($items, true);

            foreach ($items as $item) {
                DB::table('categories')->insert($item);
            }
        }

        /**
         *
         */
        $items = file_get_contents(base_path('/database/seeders/products.json'));

        if ($items) {
            $items = json_decode($items, true);
            $count = 1;
            $image_count = 1;

            foreach ($items as $item) {
                if ($image_count > 4) {
                    $image_count = 1;
                }

                $cats = Category::query()->where('parent_id', '==', 0)->with('subcategories')->get();

                $id = DB::table('products')->insertGetId([
                    'brand_id'         => rand(1, 2),
                    'action_id'        => 0,
                    'name'             => $item['name'],
                    'sku'              => '000'.$count,
                    'description'      => $item['description'],
                    'slug'             => Str::slug($item['name']),
                    'url'              => '',
                    'category_string'  => '',
                    'price'            => rand(70, 150),
                    'quantity'         => rand(10, 50),
                    'decrease'         => 1,
                    'tax_id'           => 2,
                    'special'          => 0,
                    'special_from'     => 0,
                    'special_to'       => 0,
                    'special_lock'     => 0,
                    'meta_title'       => $item['name'],
                    'meta_description' => Str::substr($item['description'], 0, 100),
                    'viewed'           => 0,
                    'sort_order'       => $count,
                    'featured'         => 0,
                    'status'           => 1,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]);

                $cat = $cats->random(1)->first();
                DB::table('product_category')->insert([
                    'product_id'    => $id,
                    'category_id'   => $cat->id,
                ]);

                $subcat = $cat->subcategories->random(1)->first();
                DB::table('product_category')->insert([
                    'product_id'    => $id,
                    'category_id'   => $subcat->id,
                ]);

                $product = Product::query()->where('id', $id)->first();

                $product->update([
                    'url'              => ProductHelper::url($product, $cat, $subcat),
                    'category_string'  => ProductHelper::categoryString($product, $cat, $subcat),
                ]);

                Storage::disk('products')->delete('/');

                $default_path = ImageHelper::save(base_path('/database/seeders/images/' . $image_count . '.jpg'), $item['name'], $id);

                $product->update([
                    'image' => 'media/img/products/' . $default_path,
                ]);

                //
                $count++;
                $image_count++;
            }
        }


        //
        $items = file_get_contents(base_path('/database/seeders/widget_groups.json'));
        if ($items) {
            $items = json_decode($items, true);
            foreach ($items as $item) {
                DB::table('widget_groups')->insert($item);
            }
        }

        $items = file_get_contents(base_path('/database/seeders/widgets.json'));
        if ($items) {
            $items = json_decode($items, true);
            foreach ($items as $item) {
                DB::table('widgets')->insert($item);
            }
        }
        
    }
}

<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Helpers\ImageHelper;
use App\Helpers\ProductHelper;
use App\Models\Back\Catalog\Brand;
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
    public function run(int $qty)
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
            $items       = json_decode($items, true);
            $count       = 1;
            $image_count = 1;

            for ($i = 0; $i < $qty; $i++) {
                if ($image_count > 4) {
                    $image_count = 1;
                }

                $cats = Category::query()->where('parent_id', '==', 0)->with('subcategories')->get();

                $names    = collect(['SPORT', 'TREK', 'AVANT', 'ECO'])->random(1)->first();
                $nosivost = collect(['120', '130', '140', '150'])->random(1)->first() . 'M';
                $promjer  = 'R' . collect(['15', '16', '17', '18', '19'])->random(1)->first();
                $sirina   = collect(['195', '205', '215'])->random(1)->first();
                $visina   = collect(['40', '45', '50', '55', '60', '75'])->random(1)->first();
                $brand    = Brand::query()->inRandomOrder()->first();

                $name        = $brand->title . ' ' . $sirina . '/' . $visina . ' ' . $promjer . ' ' . $names . ' ' . $nosivost;
                $description = "<p><strong>Učinkovitosti potrošnje goriva - C; kl. učinkovitosti na mokroj podlozi - A; mjerenje buke i otpora kotrljanja</strong></p><p>Najvažniji datum NIJE onaj proizvodnje pneumatika (DOT), već datum montaže pneumatika na vozilo.</p><p>Pravilno skladištene i nekorištene gume mogu prodavati kao nove do njihove 5 godine starosti, a voziti se mogu do njihove 10 godine starosti</p><p>Gume stare zbog toga što se u njima stalno odvijaju kemijski i fizikalni procesi. U svakom slučaju, navedeni procesi odvijaju se vrlo sporo u gumama koje su pravilno skladištene. Starenje počinje nakon montaže pneumatika na vozilo. Kraće razdoblje prije toga je zanemarivo.</p><p>Prije montaže na vaš automobil pneumatik nije napuhan, nije opterećen te je podvrgnut samo manjim temperaturnim promjenama na mjestu skladištenja – dakle ne oštećuje se. Budući da su pneumatici dizajnirani za osiguranje pouzdane upotrebe na vozilima dugi niz godina i pod brojnim različitim uvjetima upotrebe, kraće starenje do kojeg dolazi tijekom njihova skladištenja, a prije montaže, nema većeg značaja.</p><p>&nbsp;</p>";
                $quantity = rand(0, 50);

                $id = DB::table('products')->insertGetId([
                    'brand_id'         => $brand->id,
                    'action_id'        => 0,
                    'name'             => $name,
                    'sku'              => '000' . $count,
                    'description'      => $description,
                    'slug'             => Str::slug($name),
                    'url'              => '',
                    'category_string'  => '',
                    'price'            => rand(70, 150),
                    'quantity'         => $quantity,
                    'decrease'         => 1,
                    'tax_id'           => 2,
                    'special'          => 0,
                    'special_from'     => 0,
                    'special_to'       => 0,
                    'special_lock'     => 0,
                    'meta_title'       => $name,
                    'meta_description' => Str::substr($description, 0, 100),
                    'nosivost'         => $nosivost,
                    'namjena'          => collect(['Teretne', 'Auto gume', 'Off road'])->random(1)->first(),
                    'promjer'          => $promjer,
                    'sirina'           => $sirina,
                    'visina'           => $visina,
                    'buka'             => collect([60, 70, 80, 90, 100])->random(1)->first(),
                    'prijanjanje'      => collect(['A', 'B', 'C'])->random(1)->first(),
                    'iskoristivost'    => collect(['A', 'B', 'C'])->random(1)->first(),
                    'sezona'           => collect(['Zima', 'Ljeto', 'Sva'])->random(1)->first(),
                    'viewed'           => 0,
                    'sort_order'       => $count,
                    'featured'         => 0,
                    'status'           => $quantity ? 1 : 0,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]);

                $cat = $cats->random(1)->first();
                DB::table('product_category')->insert([
                    'product_id'  => $id,
                    'category_id' => $cat->id,
                ]);

                $subcat = $cat->subcategories->random(1)->first();
                DB::table('product_category')->insert([
                    'product_id'  => $id,
                    'category_id' => $subcat->id,
                ]);

                $product = Product::query()->where('id', $id)->first();

                $product->update([
                    'url'             => ProductHelper::url($product, $cat, $subcat),
                    'category_string' => ProductHelper::categoryString($product, $cat, $subcat),
                ]);

                Storage::disk('products')->delete('/');

                $default_path = ImageHelper::save(base_path('/database/seeders/images/' . $image_count . '.jpg'), $name, $id);

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

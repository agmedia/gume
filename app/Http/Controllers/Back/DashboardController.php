<?php

namespace App\Http\Controllers\Back;

use App\Helpers\Chart;
use App\Helpers\Helper;
use App\Helpers\ImageHelper;
use App\Helpers\Import;
use App\Helpers\ProductHelper;
use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use App\Mail\OrderReceived;
use App\Mail\OrderSent;
use App\Models\Back\Catalog\Author;
use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Mjerilo;
use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Catalog\Product\ProductAttribute;
use App\Models\Back\Catalog\Product\ProductCategory;
use App\Models\Back\Catalog\Product\ProductImage;
use App\Models\Back\Catalog\Publisher;
use App\Models\Back\Orders\Order;
use App\Models\Back\Orders\OrderProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Bouncer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DashboardController extends Controller
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data['today']      = Order::whereDate('created_at', Carbon::today())->count();
        $data['proccess']   = Order::whereIn('order_status_id', [1, 2, 3])->count();
        $data['finished']   = Order::whereIn('order_status_id', [4, 5, 6, 7])->count();
        $data['this_month'] = Order::whereMonth('created_at', '=', Carbon::now()->month)->count();

        $data['this_month'] = Order::whereYear('created_at', '=', Carbon::now()->year)->whereMonth('created_at', '=', Carbon::now()->month)->count();


        $orders   = Order::last()->with('products')->get();
        $products = $orders->map(function ($item) {
            return $item->products()->get();
        })->flatten();

        //dd($products);

        $chart     = new Chart();
        $this_year = json_encode($chart->setDataByYear(
            Order::chartData($chart->setQueryParams())
        ));
        $last_year = json_encode($chart->setDataByYear(
            Order::chartData($chart->setQueryParams(true))
        ));

        return view('back.dashboard', compact('data', 'orders', 'products', 'this_year', 'last_year'));
    }


    /**
     * Import initialy from Excel files.
     *
     * @param Request $request
     */
    public function import(Request $request)
    {
        $xml = simplexml_load_file(public_path('assets/pneumax.xml'));
        $import = new Import();
        $count  = 0;

        //
        $array = json_decode(json_encode($xml),TRUE);
        $sorted = collect($array['Artikl'])->whereNotIn('Namjena', ['TERETNE'])->toJson();

        //dd(collect($array['Artikl'])->count(), collect($array['Artikl'])->whereNotIn('Namjena', ['TERETNE'])->first());

        foreach (json_decode($sorted) as $item) {
            //dd($item);
            if ($item->Namjena != 'TERETNE') {
                $exist = Product::query()->where('sku', $item->Oznaka3)->first();

                if ( ! $exist) {

                    $count++;

                    $product_id = Product::insertGetId([
                        'brand_id'         => 0,
                        'action_id'        => 0,
                        'sku'              => $item->Oznaka3,
                        'ean'              => !empty($item->EAN) ? $item->EAN : '',
                        'name'             => $item->Naziv,
                        'description'      => '',
                        'slug'             => Str::slug($item->Naziv),
                        'price'            => $item->MPC,
                        'quantity'         => $item->Stock,
                        'tax_id'           => 2,
                        'special'          => null,
                        'special_lock'     => null,
                        'special_from'     => null,
                        'special_to'       => null,
                        'meta_title'       => $item->Naziv,
                        'meta_description' => '',
                        'nosivost'         => !empty($item->Li_Si) ? $item->Li_Si : '',
                        'namjena'          => !empty($item->Namjena) ? $item->Namjena : '',
                        'promjer'          => !empty($item->Promjer) ? $item->Promjer : '',
                        'sirina'           => !empty($item->Širina) ? $item->Širina : '',
                        'visina'           => !empty($item->Visina) ? $item->Visina : '',
                        'buka'             => !empty($item->Buka) ? $item->Buka : '',
                        'prijanjanje'      => !empty($item->Prianjanje_na_mokrom) ? $item->Prianjanje_na_mokrom : '',
                        'iskoristivost'    => !empty($item->Iskoristivost_goriva) ? $item->Iskoristivost_goriva : '',
                        'sezona'           => !empty($item->Namjena) ? $item->Namjena : '',
                        'viewed'           => 0,
                        'sort_order'       => 0,
                        'featured'         => 0,
                        'status'           => 1,
                        'created_at'       => Carbon::now(),
                        'updated_at'       => Carbon::now()
                    ]);

                    if ($product_id) {
                        // image
                        if ( ! empty($item->Slika1)) {
                            $image = ImageHelper::save($item->Slika1, $item->Naziv, $product_id);

                            Product::where('id', $product_id)->update([
                                'image' => $image
                            ]);
                        }
                        // + image
                        if ( ! empty($item->Informacijski_list)) {
                            $pimage = ImageHelper::save($item->Informacijski_list, $item->Naziv . '-informacijski-list', $product_id);

                            ProductImage::insert([
                                'product_id' => $product_id,
                                'image'      => $pimage,
                                'alt'        => $item->Naziv . ' Informacijski list',
                                'published'  => 1,
                                'sort_order' => 1,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }

                        // category
                        if ( ! empty($item->Kategorija)) {
                            $cat_id = $import->saveCategory($item->Kategorija);

                            if ($cat_id) {
                                ProductCategory::query()->insert([
                                    'product_id'  => $product_id,
                                    'category_id' => $cat_id,
                                ]);

                                // subcategory
                                if ( ! empty($item->Podkategorija)) {
                                    $subcat_id = $import->saveCategory($item->Podkategorija, $cat_id);

                                    if ($subcat_id) {
                                        ProductCategory::query()->insert([
                                            'product_id'  => $product_id,
                                            'category_id' => $subcat_id,
                                        ]);
                                    }
                                }
                            }
                        }

                        $product = Product::find($product_id);
                        $product->update([
                            'url' => ProductHelper::url($product),
                            'category_string' => ProductHelper::categoryString($product)
                        ]);

                        // Brand
                        if ( ! empty($item->Brand)) {
                            $brand_id = $import->resolveBrand($item->Brand);

                            $product->update(['brand_id' => $brand_id]);
                        }

                        // Attributes
                        if ( ! empty($item->EPREL_link)) {
                            $att_id = $import->resolveAttribute('EPREL Link');

                            ProductAttribute::query()->insert([
                                'product_id'   => $product_id,
                                'attribute_id' => $att_id,
                                'value'        => $item->EPREL_link,
                            ]);
                        }
                        if ( ! empty($item->Dezen)) {
                            $att_id = $import->resolveAttribute('Dezen gume');

                            ProductAttribute::query()->insert([
                                'product_id'   => $product_id,
                                'attribute_id' => $att_id,
                                'value'        => $item->Dezen,
                            ]);
                        }
                        if ( ! empty($item->SAP_kod)) {
                            $att_id = $import->resolveAttribute('SAP Kod');

                            ProductAttribute::query()->insert([
                                'product_id'   => $product_id,
                                'attribute_id' => $att_id,
                                'value'        => $item->SAP_kod,
                            ]);
                        }

                        $count++;

                        if ($count > 200) {
                            return redirect()->route('dashboard');
                        }
                    }
                }
            }
        }

        return redirect()->route('dashboard')->with(['success' => 'Import je uspješno obavljen..! ' . $count . ' proizvoda importano.']);
    }


    /**
     * Import initialy from Excel files.
     *
     * @param Request $request
     */
    public function importInter(Request $request)
    {
        $category = 12;
        $subcategory = 39;
        $sync_cat = 'Metlica brisača';
        //gume
        //$xml = simplexml_load_file('https://feeds.datafeedwatch.com/70335/d2bfb7399e3bee04d0dabb9b5f0954de960f8569.xml');
        //dijelovi
        $xml = simplexml_load_file('https://feeds.datafeedwatch.com/70335/d8aa73ceb924b75fd493399154b0c61f3ec93178.xml');

        $import = new Import();
        $count  = 0;

        $group = [];
        foreach ($xml->children() as $item) {
            $cat = (string) $item->category;

            if (strpos($cat, $sync_cat) !== false) {
                $sku = (string) $item->product_code;
                $exist = Product::query()->where('sku', $sku)->first();

                if ( ! $exist) {
                    $product_id = Product::insertGetId([
                        'brand_id'         => 0,
                        'action_id'        => 0,
                        'sku'              => $sku,
                        'ean'              => !empty((string) $item->ean) ? (string) $item->ean : '',
                        'name'             => (string) $item->product_name,
                        'description'      => (string) $item->description,
                        'slug'             => Str::slug((string) $item->product_name),
                        'price'            => (string) $item->price,
                        'quantity'         => (string) $item->stock_number,
                        'tax_id'           => 2,
                        'special'          => null,
                        'special_lock'     => null,
                        'special_from'     => null,
                        'special_to'       => null,
                        'meta_title'       => (string) $item->product_name,
                        'meta_description' => '',
                        'nosivost'         => !empty((string) $item->carrying_capacity_index) ? (string) $item->carrying_capacity_index : '',
                        'namjena'          => !empty((string) $item->tyre_type) ? (string) $item->tyre_type : '',
                        'promjer'          => !empty((string) $item->dimensions_diameter) ? (string) $item->dimensions_diameter : '',
                        'sirina'           => !empty((string) $item->dimensions_width) ? (string) $item->dimensions_width : '',
                        'visina'           => !empty((string) $item->dimensions_height) ? (string) $item->dimensions_height : '',
                        'buka'             => !empty((string) $item->external_rolling_noise) ? (string) $item->external_rolling_noise : '',
                        'prijanjanje'      => !empty((string) $item->grip_on_wet_roads) ? (string) $item->grip_on_wet_roads : '',
                        'iskoristivost'    => !empty((string) $item->fuel_efficiency) ? (string) $item->fuel_efficiency : '',
                        'sezona'           => '',
                        'viewed'           => 0,
                        'sort_order'       => 0,
                        'featured'         => 0,
                        'status'           => 1,
                        'created_at'       => Carbon::now(),
                        'updated_at'       => Carbon::now()
                    ]);

                    if ($product_id) {
                        // image
                        if ( ! empty((string) $item->URL_to_product_image)) {
                            $image = ImageHelper::save((string) $item->URL_to_product_image, (string) $item->product_name, $product_id);

                            Product::where('id', $product_id)->update([
                                'image' => $image
                            ]);
                        }

                        // Category
                        ProductCategory::query()->insert([
                            'product_id'  => $product_id,
                            'category_id' => $category,
                        ]);
                        ProductCategory::query()->insert([
                            'product_id'  => $product_id,
                            'category_id' => $subcategory,
                        ]);

                        $product = Product::find($product_id);
                        $product->update([
                            'url' => ProductHelper::url($product),
                            'category_string' => ProductHelper::categoryString($product)
                        ]);

                        // Brand
                        if ( ! empty((string) $item->brand)) {
                            $brand_id = $import->resolveBrand($item->brand);

                            $product->update(['brand_id' => $brand_id]);
                        }

                        // Attributes
                        if ( ! empty((string) $item->speed_index)) {
                            $att_id = $import->resolveAttribute('Index Brzine');

                            ProductAttribute::query()->insert([
                                'product_id'   => $product_id,
                                'attribute_id' => $att_id,
                                'value'        => (string) $item->speed_index,
                            ]);
                        }

                        $count++;

                        if ($count > 500) {
                            return redirect()->route('dashboard');
                        }
                    }
                }
            }
        }

        return redirect()->route('dashboard')->with(['success' => 'Import je uspješno obavljen..! ' . $count . ' proizvoda importano.']);
    }




    /**
     * Set up roles. Should be done once only.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setRoles()
    {
        if ( ! auth()->user()->can('*')) {
            abort(401);
        }

        $superadmin = Bouncer::role()->firstOrCreate([
            'name'  => 'superadmin',
            'title' => 'Super Administrator',
        ]);

        Bouncer::role()->firstOrCreate([
            'name'  => 'admin',
            'title' => 'Administrator',
        ]);

        Bouncer::role()->firstOrCreate([
            'name'  => 'editor',
            'title' => 'Editor',
        ]);

        Bouncer::role()->firstOrCreate([
            'name'  => 'customer',
            'title' => 'Customer',
        ]);

        Bouncer::allow($superadmin)->everything();

        Bouncer::ability()->firstOrCreate([
            'name'  => 'set-super',
            'title' => 'Postavi korisnika kao Superadmina.'
        ]);

        $users = User::whereIn('email', ['filip@agmedia.hr', 'tomislav@agmedia.hr'])->get();

        foreach ($users as $user) {
            $user->assign($superadmin);
        }

        return redirect()->route('dashboard');
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function letters()
    {
        $authors = Author::all();

        foreach ($authors as $author) {
            $letter = Helper::resolveFirstLetter($author->title);

            $author->update([
                'letter' => Str::ucfirst($letter)
            ]);
        }

        //
        $publishers = Publisher::all();

        foreach ($publishers as $publisher) {
            $letter = Helper::resolveFirstLetter($publisher->title);

            $publisher->update([
                'letter' => Str::ucfirst($letter)
            ]);
        }

        return redirect()->route('dashboard');
    }


    /**
     *
     */
    public function slugs()
    {
        $slugs = Product::query()->groupBy('slug')->havingRaw('COUNT(id) > 1')->pluck('slug', 'id')->toArray();

        foreach ($slugs as $slug) {
            $products = Product::where('slug', $slug)->get();

            if ($products) {
                foreach ($products as $product) {
                    $time = Str::random(9);
                    $product->update([
                        'slug' => $product->slug . '-' . $time,
                        'url' => $product->url . '-' . $time,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard');
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function statuses()
    {
        // AUTHORS
        $products = Product::query()
                           ->where('quantity', '>', 0)
                           ->select('author_id', DB::raw('count(*) as total'))
                           ->groupBy('author_id')
                           ->pluck('author_id')
                           ->unique();

        $authors = Author::query()->pluck('id')->diff($products)->flatten();

        Author::whereIn('id', $authors)->update([
            'status' => 0,
            'updated_at' => now()
        ]);

        Author::whereNotIn('id', $authors)->update([
            'status' => 1,
            'updated_at' => now()
        ]);

        // PUBLISHERS
        $products = Product::query()
                           ->where('quantity', '>', 0)
                           ->select('publisher_id', DB::raw('count(*) as total'))
                           ->groupBy('publisher_id')
                           ->pluck('publisher_id')
                           ->unique();

        $publishers = Publisher::query()->pluck('id')->diff($products)->flatten();

        Publisher::whereIn('id', $publishers)->update([
            'status' => 0,
            'updated_at' => now()
        ]);

        Publisher::whereNotIn('id', $publishers)->update([
            'status' => 1,
            'updated_at' => now()
        ]);

        // CATEGORIES
        $categories_off = Category::query()->select('id')->withCount('products')->having('products_count', '<', 1)->get()->toArray();

        if ($categories_off) {
            foreach ($categories_off as $category) {
                Category::where('id', $category['id'])->update([
                    'status' => 0,
                    'updated_at' => now()
                ]);
            }
        }

        $categories_on = Category::query()->select('id')->withCount('products')->having('products_count', '>', 0)->get()->toArray();

        if ($categories_on) {
            foreach ($categories_on as $category) {
                Category::where('id', $category['id'])->update([
                    'status' => 1,
                    'updated_at' => now()
                ]);
            }
        }

        // PRODUCTS
        $products = Product::where('quantity', 0)->pluck('id');

        Product::whereIn('id', $products)->update([
            'status' => 0,
            'updated_at' => now()
        ]);

        Product::whereNotIn('id', $products)->update([
            'status' => 1,
            'updated_at' => now()
        ]);

        return redirect()->route('dashboard');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mailing(Request $request)
    {
        $order = Order::where('id', 3)->first();

        dispatch(function () use ($order) {
            Mail::to(config('mail.admin'))->send(new OrderReceived($order));
            Mail::to($order->payment_email)->send(new OrderSent($order));
        });

        return redirect()->route('dashboard');
    }


    /**
     *
     */
    public function duplicate(string $target = null)
    {
        // Duplicate images
        if ($target === 'images') {
            $paths = ProductImage::query()->groupBy('image')->havingRaw('COUNT(id) > 1')->pluck('image', 'id')->toArray();

            foreach ($paths as $path) {
                $first = ProductImage::where('image', $path)->first();

                ProductImage::where('image', $path)->where('id', '!=', $first->id)->delete();
            }
        }

        // Duplicate publishers
        if ($target === 'publishers') {
            $paths = Publisher::query()->groupBy('title')->havingRaw('COUNT(id) > 1')->pluck('title', 'id')->toArray();

            foreach ($paths as $id => $path) {
                $group = Publisher::where('title', $path)->get();

                foreach ($group as $item) {
                    if ($item->id != $id) {
                        foreach ($item->products()->get() as $product) {
                            Product::where('id', $product->id)->update([
                                'publisher_id' => $id
                            ]);
                        }

                        Publisher::where('id', $item->id)->delete();
                    }
                }
            }
        }

        return redirect()->route('dashboard');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setCategoryGroup(Request $request)
    {
        Category::query()->update([
            'group' => Helper::categoryGroupPath(true)
        ]);

        $products = Product::query()->where('push', 0)->get();

        foreach ($products as $product) {
            $product->update([
                'url'             => ProductHelper::url($product),
                'category_string' => ProductHelper::categoryString($product),
                'push'            => 1
            ]);
        }

        return redirect()->route('dashboard');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setProductsUnlimitedQty(Request $request)
    {
        $products = ProductCategory::query()->where('category_id', 25)->pluck('product_id');

        Product::query()->whereIn('id', $products)->update([
            'quantity' => 100,
            'decrease' => 0,
            'status' => 1
        ]);

        return redirect()->route('dashboard')->with(['success' => 'Proizvodi su namješteni na neograničenu količinu..! ' . $products->count() . ' proizvoda obnovljeno.']);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPdvProducts(Request $request)
    {
        $ids = ProductCategory::query()->whereIn('category_id', [174, 175])->pluck('product_id');

        Product::query()->whereIn('id', $ids)->update([
            'tax_id' => 2
        ]);

        return redirect()->route('dashboard')->with(['success' => 'PDV je obnovljen na kategoriji svezalice..! ' . $ids->count() . ' proizvoda obnovljeno.']);
    }

}

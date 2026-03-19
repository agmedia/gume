<?php

namespace App\Http\Controllers\Back;

use App\Helpers\Chart;
use App\Helpers\Helper;
use App\Helpers\ImageHelper;
use App\Helpers\Import;
use App\Helpers\OrderHelper;
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
use App\Models\Back\Settings\Api\DataFeedWatch;
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
    public function importOld(Request $request)
    {
        $xml = simplexml_load_file(public_path('assets/pneu-new.xml'));
        $import = new Import();
        $count  = 0;

        //
        $array = json_decode(json_encode($xml),TRUE);
        $sorted = collect($array['Artikl'])->whereNotIn('Namjena', ['TERETNE'])->toJson();

        //dd(collect($array['Artikl'])->count(), collect($array['Artikl'])->whereNotIn('Namjena', ['TERETNE'])->first());

        foreach (json_decode($sorted) as $item) {
            //dd($item);
            if ($item->Namjena != 'TERETNE') {
                $exist = Product::query()->where('ean', $item->EAN)->first();

                if ( $exist) {

                    $count++;


                    $images = ProductAttribute::query()->where('product_id', $exist->id)->where('attribute_id', 1)->count();

                    if (!$images) {

                        // Attributes
                        if (!empty($item->EPREL_link)) {
                            $att_id = $import->resolveAttribute('EPREL Link');

                            ProductAttribute::query()->insert([
                                'product_id' => $exist->id,
                                'attribute_id' => $att_id,
                                'value' => $item->EPREL_link,
                            ]);
                        }


                        $count++;

                        if ($count > 5000) {
                            return redirect()->route('dashboard');
                        }
                    }
                }
                }

        }

        return redirect()->route('dashboard')->with(['success' => 'Import je uspješno obavljen..! ' . $count . ' proizvoda importano.']);
    }


    public function import(Request $request)
    {

        $category = 46;
        $subcategory = 35;



        $xml = simplexml_load_file(public_path('assets/pneumaxnew.xml'));
        $import = new Import();
        $count  = 0;



        //
        $array = json_decode(json_encode($xml),TRUE);
        $sorted = collect($array['Artikl'])->whereNotIn('Namjena', ['TERETNE'])->toJson();

        //dd(collect($array['Artikl'])->count(), collect($array['Artikl'])->whereNotIn('Namjena', ['TERETNE'])->first());

        foreach (json_decode($sorted) as $item) {
            //dd($item);
            if ($item->Namjena != 'TERETNE' and $item->Kategorija =='GUME' and $item->Podkategorija =='ZIMSKE 4 X 4' and $item->Brand =='SAVA') {
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
                        'namjena'          => 'Guma za osobna vozila',
                        'promjer'          => !empty($item->Promjer) ? $item->Promjer : '',
                        'sirina'           => !empty($item->Širina) ? $item->Širina : '',
                        'visina'           => !empty($item->Visina) ? $item->Visina : '',
                        'buka'             => !empty($item->Buka) ? $item->Buka : '',
                        'prijanjanje'      => !empty($item->Prianjanje_na_mokrom) ? $item->Prianjanje_na_mokrom : '',
                        'iskoristivost'    => !empty($item->Iskoristivost_goriva) ? $item->Iskoristivost_goriva : '',
                        'sezona'           => 'Ljeto',
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

                        // This legacy import always maps to predefined local categories.
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

                        if ($count > 300) {
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
        $report = (new DataFeedWatch())->syncProducts([
            'import_missing' => true,
        ]);

        return redirect()->route('dashboard')->with([
            'success' => sprintf(
                'Feed import/update je obavljen. Kreirano: %d | Ažurirano: %d | Preskočeno: %d | Greške: %d',
                $report['created'],
                $report['updated'],
                $report['skipped'],
                $report['failed']
            ),
        ]);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePQ(Request $request)
    {
        $report = (new DataFeedWatch())->syncProducts([
            'import_missing' => false,
        ]);

        return redirect()->route('dashboard')->with([
            'success' => sprintf(
                'Feed update je obavljen. Ažurirano: %d | Preskočeno: %d | Greške: %d',
                $report['updated'],
                $report['skipped'],
                $report['failed']
            ),
        ]);
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
        $order = OrderHelper::get(1);

        if ($order->isValid()) {
            $order->sendEmails()
                  ->decreaseCartItems()
                  ->getOrder();
        }
        dd($order->getOrder()->reservation);
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

<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Breadcrumb;
use App\Helpers\Helper;
use App\Helpers\Query;
use App\Helpers\RouteResolver;
use App\Http\Controllers\FrontController;
use App\Imports\ProductImport;
use App\Models\Front\Blog;
use App\Models\Front\Page;
use App\Models\Front\Faq;
use App\Models\Front\Catalog\Brand;
use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CatalogRouteController extends FrontController
{

    /**
     * Resolver for the Groups, categories and products routes.
     * Route::get('{group}/{cat?}/{subcat?}/{prod?}', 'Front\GCP_RouteController::resolve()')->name('gcp_route');
     *
     * @param               $group
     * @param Category|null $cat
     * @param Category|null $subcat
     * @param Product|null  $prod
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resolve(Request $request, string $group, string $cat = null, string $subcat = null, Product $prod = null)
    {
        $route = new RouteResolver($group, $cat, $subcat, $prod);

        $route->checkForUnwantedPaths();

        $route->setRoute()->isAllowedGroup();

        $data = $route->getData();

        // Provjeri ako je proizvod setan u ruti.
        if ($data->product) {
            $data->product->increment('viewed', 1);

            $meta = Seo::getProductData($prod);

            $related_products = Product::where('featured', '1')->get();

            $crumbs = (new Breadcrumb())->product($data->group, $data->category, $data->subcategory, $data->product)->resolve();

            return view('front.catalog.product.index', compact('data', 'meta', 'crumbs','related_products'));
        }

        // Nastavi sa prikazom kategorije sa listom proizvoda.
        $meta = Seo::getMetaTags($request, 'filter');
        $crumbs = (new Breadcrumb())->category($data->group, $data->category, $data->subcategory)->resolve();

        return view('front.catalog.category.index', compact('data', 'crumbs', 'meta'));
    }


    /**
     *
     *
     * @param Author $author
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function brand(Request $request, Brand $brand = null, Category $cat = null, Category $subcat = null)
    {
        if ( ! $brand) {
            $letters = Helper::resolveCache('authors')->remember('letters', config('cache.life'), function () {
                return Author::letters();
            });
            $letter = $this->checkLetter($letters);

            if ($request->has('letter')) {
                $letter = $request->input('letter');
            }

            $currentPage = request()->get('page', 1);

            $brands = Helper::resolveCache('authors')->remember($letter . '.' . $currentPage, config('cache.life'), function () use ($letter) {
                return Brand::query()->select('id', 'title', 'url')
                                      ->where('status',  1)
                                      ->where('letter', $letter)
                                      ->orderBy('title')
                                      ->withCount('products')
                                      ->paginate(36)
                                      ->appends(request()->query());
            });

            $meta_tags = Seo::getMetaTags($request, 'ap_filter');

            return view('front.catalog.authors.index', compact('brands', 'letters', 'letter', 'meta_tags'));
        }

        $letter = null;

        if ($cat) { $cat->count = $cat->products()->count(); }
        if ($subcat) { $subcat->count = $subcat->products()->count(); }

        $seo = Seo::getAuthorData($brand, $cat, $subcat);

        $crumbs = null;

        return view('front.catalog.category.index', compact('brand', 'letter', 'cat', 'subcat', 'seo', 'crumbs'));
    }


    /**
     *
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if ($request->has(config('settings.search_keyword'))) {
            if ( ! $request->input(config('settings.search_keyword'))) {
                return redirect()->back()->with(['error' => 'Oops..! Zaboravili ste upisati pojam za pretraživanje..!']);
            }

            $ids = Query::search(
                $request->input(config('settings.search_keyword'))
            );

            $data = new \stdClass();
            $data->ids = $ids;
            $data->category = null;
            $data->subcategory = null;

            $crumbs = null; $meta = null;

            return view('front.catalog.category.index', compact('data', 'meta', 'crumbs'));
        }

        return response()->json(['error' => 'Greška kod pretrage..! Molimo pokušajte ponovo ili nas kotaktirajte! HVALA...']);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function actions(Request $request, Category $cat = null, $subcat = null)
    {
        $ids = Product::query()->whereNotNull('special')->pluck('id');
        $group = 'snizenja';

        $crumbs = null;

        return view('front.catalog.category.index', compact('group', 'cat', 'subcat', 'ids', 'crumbs'));
    }


    /**
     * @param Page $page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function page(Page $page)
    {
        return view('front.page', compact('page'));
    }


    /**
     * @param Blog $blog
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function blog(Blog $blog)
    {
        if (! $blog->exists) {
            $blogs = Blog::active()->get();

            return view('front.blog', compact('blogs'));
        }

        return view('front.blog', compact('blog'));
    }


    /**
     * @param Faq $faq
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function faq()
    {
        $faq = Faq::where('status', 1)->get();
        return view('front.faq', compact('faq'));
    }


    /**
     * @param array $letters
     *
     * @return string
     */
    private function checkLetter(Collection $letters): string
    {
        foreach ($letters->all() as $letter) {
            if ($letter['active']) {
                return $letter['value'];
            }
        }

        return 'A';
    }

}

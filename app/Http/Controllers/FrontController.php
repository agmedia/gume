<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Back\Settings\Settings;
use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Page;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class FrontController extends Controller
{

    public function __construct()
    {
        $uvjeti_kupnje = Page::where('subgroup', 'Uvjeti kupnje')->get();
        View::share('uvjeti_kupnje', $uvjeti_kupnje);

        $nacini_placanja = Page::where('subgroup', 'Načini plaćanja')->get();
        View::share('nacini_placanja', $nacini_placanja);

        $products = Product::active()->hasStock()->count();
        View::share('products', $products);

        $users = User::count();
        View::share('users', $users);

        // $category = (new Category())->getList(true);
        //  View::share('category_list', $category);

        $category = Category::active()->topList(Helper::categoryGroupPath(true))->sortByName()->select('id', 'title', 'group', 'slug')->get();
        View::share('category_list', $category);

        $kategorijefeatured = Category::active()->where('image', '!=', 'media/avatars/avatar0.jpg')->sortByName()->select('id','image','title', 'group', 'slug')->get();
        View::share('kategorijefeatured', $kategorijefeatured);

        $zemljovidi_vedute = Category::active()->topList('Zemljovidi i vedute')->select('id', 'title', 'group', 'slug')->sortByName()->get();
        View::share('zemljovidi_vedute', $zemljovidi_vedute);
    }

}

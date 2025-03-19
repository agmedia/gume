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

        /*$users = User::count();
        View::share('users', $users);*/

        $category = Category::active()->topList(Helper::categoryGroupPath(true))->sortByName()->select('id', 'title', 'group', 'slug')->get();
        View::share('category_list', $category);

    }

}

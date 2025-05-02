<?php


namespace App\Helpers;


use App\Models\Front\Catalog\Author;
use App\Models\Front\Catalog\Brand;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Catalog\Publisher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Query
{

    /**
     * @param string $author
     *
     * @return array
     */
    public static function mountAuthor(string $author): array
    {
        $response = [];

        if (strpos($author, ',') !== false) {
            $arr = explode(',', $author);

            foreach ($arr as $item) {
                $_author = Author::where('slug', $item)->first();
                $response[$_author->id] = $item;
            }
        } else {
            $_author = Author::where('slug', $author)->first();
            $response[$_author->id] = $author;
        }

        return $response;
    }


    /**
     * @param string $publisher
     *
     * @return array
     */
    public static function mountPublisher(string $publisher): array
    {
        $response = [];

        if (strpos($publisher, ',') !== false) {
            $arr = explode(',', $publisher);

            foreach ($arr as $item) {
                $_publisher = Publisher::where('slug', $item)->first();
                $response[$_publisher->id] = $item;
            }
        } else {
            $_publisher = Publisher::where('slug', $publisher)->first();
            $response[$_publisher->id] = $publisher;
        }

        return $response;
    }


    /**
     * @param array $data
     *
     * @return string
     */
    public static function resolve(array $data): string
    {
        $response = '';

        foreach ($data as $item) {
            if ($item) {
                $response .= $item . ',';
            }
        }

        if ( ! $data) {
            $response = '';
        } else {
            $response = substr($response, 0, -1);
        }

        return $response;
    }


    /**
     * @param array $data
     *
     * @return array
     */
    public static function unset(array $data): array
    {
        foreach ($data as $key => $item) {
            if ( ! $item) {
                unset($data[$key]);
            }
        }

        return $data;
    }


    /**
     * @param string $target
     * @param bool   $builder
     *
     * @return array|false|Collection
     */
    public static function search(string $target = '', bool $builder = false)
    {
        if ($target != '') {
            $response = collect();

            $preg = explode(' ', $target, 3);

            if (isset ($preg[1]) && in_array($preg[1], $preg) && ! isset($preg[2])) {
                $products =  Product::active()->where('name', 'like', '%' . $preg[0] . '%' . $preg[1] . '%')
                    ->orWhere('name', 'like', '%' . $preg[1] . '% ' . $preg[0] . '%')
                    ->orWhere('meta_title', 'like', '%' . $target . '%')
                    ->orWhere('description', 'like', '%' . $preg[0] . '%' . $preg[1] . '%')
                    ->orWhere('description', 'like', '%' . $preg[1] . '% ' . $preg[0] . '%')
                    ->orWhere('nosivost', 'like', '%' . $target . '%')
                    ->orWhere('promjer', 'like', '%' . $target . '%')
                    ->orWhere('sirina', 'like', '%' . $target . '%')
                    ->orWhere('visina', 'like', '%' . $target . '%')
                    ->orWhere('buka', 'like', '%' . $target . '%')
                    ->orWhere('sku', 'like', '%' . $target . '%')
                    ->pluck('id');
            } elseif (isset ($preg[2]) && in_array($preg[2], $preg)) {
                $products = Product::active()->where('name', 'like', $preg[0] . '%' . $preg[1] . '%' . $preg[2] . '%')
                    ->orWhere('name', 'like', $preg[2] . '%' . $preg[1] . '% ' . $preg[0] . '%')
                    ->orWhere('name', 'like', $preg[0] . '%' . $preg[2] . '% ' . $preg[1] . '%')
                    ->orWhere('name', 'like', $preg[1] . '%' . $preg[0] . '% ' . $preg[2] . '%')
                    ->orWhere('name', 'like', $preg[1] . '%' . $preg[2] . '% ' . $preg[0] . '%')
                    ->orWhere('meta_title', 'like', '%' . $target . '%')
                    ->orWhere('description', 'like', $preg[0] . '%' . $preg[1] . '%' . $preg[2] . '%')
                    ->orWhere('description', 'like', $preg[2] . '%' . $preg[1] . '% ' . $preg[0] . '%')
                    ->orWhere('description', 'like', $preg[0] . '%' . $preg[2] . '% ' . $preg[1] . '%')
                    ->orWhere('description', 'like', $preg[1] . '%' . $preg[0] . '% ' . $preg[2] . '%')
                    ->orWhere('description', 'like', $preg[1] . '%' . $preg[2] . '% ' . $preg[0] . '%')
                    ->orWhere('nosivost', 'like', '%' . $target . '%')
                    ->orWhere('promjer', 'like', '%' . $target . '%')
                    ->orWhere('sirina', 'like', '%' . $target . '%')
                    ->orWhere('visina', 'like', '%' . $target . '%')
                    ->orWhere('buka', 'like', '%' . $target . '%')
                    ->orWhere('sku', 'like', '%' . $target . '%')
                    ->pluck('id');
            } else {
                $products = Product::active()->where('name', 'like', '%' . $preg[0] . '%')
                    ->orWhere('meta_title', 'like', '%' . $target . '%')
                    ->orWhere('description', 'like', '%' . $preg[0] . '%')
                    ->orWhere('nosivost', 'like', '%' . $target . '%')
                    ->orWhere('promjer', 'like', '%' . $target . '%')
                    ->orWhere('sirina', 'like', '%' . $target . '%')
                    ->orWhere('visina', 'like', '%' . $target . '%')
                    ->orWhere('buka', 'like', '%' . $target . '%')
                    ->orWhere('sku', 'like', '%' . $target . '%')
                    ->pluck('id');
            }


            if ( ! $products->count()) {
                $products = collect();
            }

            /*$preg = explode(' ', $target, 3);

            if (isset ($preg[1]) && in_array($preg[1], $preg) && ! isset($preg[2])) {
                $authors = Brand::active()->where('title', 'like', '%' . $preg[0] . '%' . $preg[1] . '%')
                                ->orWhere('title', 'like', '%' . $preg[1] . '% ' . $preg[0] . '%')
                                ->with('products')->get();

            } elseif (isset ($preg[2]) && in_array($preg[2], $preg)) {
                $authors = Brand::active()->where('title', 'like', $preg[0] . '%' . $preg[1] . '%' . $preg[2] . '%')
                                ->orWhere('title', 'like', $preg[2] . '%' . $preg[1] . '% ' . $preg[0] . '%')
                                ->orWhere('title', 'like', $preg[0] . '%' . $preg[2] . '% ' . $preg[1] . '%')
                                ->orWhere('title', 'like', $preg[1] . '%' . $preg[0] . '% ' . $preg[2] . '%')
                                ->orWhere('title', 'like', $preg[1] . '%' . $preg[2] . '% ' . $preg[0] . '%')
                                ->with('products')->get();

            } else {
                $authors = Brand::active()->where('title', 'like', '%' . $preg[0] . '%')
                                ->with('products')->get();
            }

            foreach ($authors as $author) {
                $products = $products->merge($author->products->pluck('id'));
            }*/

            $response->put('products', $products->unique()->flatten());

            if ($builder) {
                return $response;
            }

            return $response['products']->toJson();
        }

        return false;
    }


    /**
     * @param Builder $query
     * @param string  $search
     *
     * @return Builder
     */
    public static function searchByTitle(Builder $query, string $search): Builder
    {
        $preg = explode(' ', $search, 3);

        if (isset ($preg[1]) && in_array($preg[1], $preg) && ! isset($preg[2])) {
            $query->where('title', 'like', '%' . $preg[0] . '%' . $preg[1] . '%')
                  ->orWhere('title', 'like', '%' . $preg[1] . '% ' . $preg[0] . '%');

        } elseif (isset ($preg[2]) && in_array($preg[2], $preg)) {
            $query->where('title', 'like', $preg[0] . '%' . $preg[1] . '%' . $preg[2] . '%')
                  ->orWhere('title', 'like', $preg[2] . '%' . $preg[1] . '% ' . $preg[0] . '%')
                  ->orWhere('title', 'like', $preg[0] . '%' . $preg[2] . '% ' . $preg[1] . '%')
                  ->orWhere('title', 'like', $preg[1] . '%' . $preg[0] . '% ' . $preg[2] . '%')
                  ->orWhere('title', 'like', $preg[1] . '%' . $preg[2] . '% ' . $preg[0] . '%');

        } else {
            $query->where('title', 'like', '%' . $preg[0] . '%');
        }

        return $query;
    }

}

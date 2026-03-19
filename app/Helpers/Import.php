<?php

namespace App\Helpers;

use App\Models\Back\Catalog\Attributes\Attributes;
use App\Models\Back\Catalog\Author;
use App\Models\Back\Catalog\Brand;
use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Publisher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class Import
{

    /**
     * @param array  $images
     * @param string $name
     * @param int    $id
     *
     * @return array
     */
    public function resolveImages(array $images, string $name, int $id): array
    {
        $response = [];

        foreach ($images as $key => $image) {
            if ($image) {
                $time = time() . Str::random(9);

                $image_saved = Storage::disk('local')->put('temp/' . $key . '.jpg', file_get_contents($image));

                if ($image_saved) {
                    try {
                        $image = Storage::disk('local')->get('temp/' . $key . '.jpg');
                        $img = Image::make($image);
                    } catch (\Exception $e) {
                        Log::info('Error downloading image: ' . $image);
                        Log::info($e->getMessage());
                    }

                    $str = $id . '/' . Str::limit(Str::slug($name)) . '-' . $time . '.';

                    $path = $str . 'jpg';
                    Storage::disk('products')->put($path, $img->encode('jpg'));

                    $path_webp = $str . 'webp';
                    Storage::disk('products')->put($path_webp, $img->encode('webp'));

                    // Thumb creation
                    $str_thumb = $id . '/' . Str::limit(Str::slug($name)) . '-' . $time . '-thumb.';

                    $img = $img->resize(null, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->fit(250, 300);

                    $path_webp_thumb = $str_thumb . 'webp';
                    Storage::disk('products')->put($path_webp_thumb, $img->encode('webp'));

                    $response[] = config('filesystems.disks.products.url') . $path;

                    Storage::disk('local')->delete('temp/' . $key . '.jpg');
                }
            }
        }

        return $response;
    }

    /**
     * @param array $categories
     *
     * @return int|mixed
     */
    public function resolveStringCategories(string $categories)
    {
        $default = config('settings.eng_default_category');
        $response[] = $default;

        $categories = explode(', ', $categories);

        if ( ! isset($categories[1])) {
            $response[] = $this->saveCategory($categories[0]);
        } else {
            $response[] = $this->saveCategory($categories[0]);
            $response[] = $this->saveCategory($categories[1]);
        }

        return $response;
    }


    /**
     * @param array $categories
     *
     * @return int|mixed
     */
    public function resolveCategories(array $categories)
    {
        $response = [];

        foreach ($categories as $category) {
            if ($category != 'Akcijska ponuda') {
                $category = $this->replaceNames($category);

                if ( ! str_contains($category, '>')) {
                    $response[] = $this->saveCategory($category);
                } else {
                    $parent_id = 0;
                    $cats = explode('>', $category);

                    foreach ($cats as $key => $cat) {
                        if ($key == 0) {
                            $parent_id = $this->saveCategory($cat);
                            $response[] = $parent_id;
                        } else {
                            $id = $this->saveCategory($cat, $parent_id);
                            $response[] = $id;
                        }
                    }
                }
            }
        }

        if (empty($response)) {
            $response[] = 1;
        }

        return $response;
    }


    /**
     * Resolve only categories that already exist in the catalog tree.
     *
     * @param array $categories
     *
     * @return array
     */
    public function resolveExistingCategories(array $categories): array
    {
        $response = [];

        foreach ($categories as $category) {
            if ($category == 'Akcijska ponuda') {
                continue;
            }

            $category = $this->replaceNames($category);
            $segments = array_values(array_filter(array_map('trim', explode('>', $category))));

            if (empty($segments)) {
                continue;
            }

            $parentId = 0;

            foreach ($segments as $segment) {
                $existing = $this->findExistingCategory($segment, $parentId);

                if ( ! $existing) {
                    break;
                }

                $parentId = $existing->id;
                $response[] = $existing->id;
            }
        }

        return array_values(array_unique($response));
    }


    /**
     * @param string $name
     * @param int    $parent
     *
     * @return mixed
     */
    public function saveCategory(string $name, int $parent = 0)
    {
        $exist = Category::where('title', $name)->first();

        if ( ! $exist) {
            return Category::insertGetId([
                'parent_id'        => $parent,
                'title'            => $name,
                'description'      => '',
                'meta_title'       => $name,
                'meta_description' => $name,
                'group'            => Helper::categoryGroupPath(true),
                'lang'             => 'hr',
                'status'           => 1,
                'slug'             => Str::slug($name),
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now()
            ]);
        }

        return $exist->id;
    }


    /**
     * @param string $author
     *
     * @return int
     */
    public function resolveBrand(string $brand = null): int
    {
        if ($brand) {
            $exist = Brand::where('title', $brand)->first();

            if ( ! $exist) {
                $slug = Str::slug($brand);

                return Brand::insertGetId([
                    'letter'           => Helper::resolveFirstLetter($brand),
                    'title'            => $brand,
                    'description'      => '',
                    'meta_title'       => $brand,
                    'meta_description' => '',
                    'slug'             => $slug,
                    'url'              => config('settings.brand_path') . '/' . $slug,
                    'sort_order'       => 0,
                    'status'           => 1,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now()
                ]);
            }

            return $exist->id;
        }

        return config('settings.unknown_brand');
    }


    /**
     * @param string      $start_tag
     * @param string|null $end_tag
     *
     * @return string
     */
    public function resolveContent(string $content, string $start_tag, string $end_tag = null): string
    {
        $content = strip_tags($content);
        $ini = strlen($start_tag);
        $len = strpos($content, $end_tag, $ini) - $ini;

        return trim(substr($content, $ini, $len));
    }


    /**
     * @param string|null $title
     *
     * @return int
     */
    public function resolveAttribute(string $title = null): int
    {
        if ($title) {
            $exist = Attributes::query()->where('title', $title)->first();

            if ( ! $exist) {
                return Attributes::insertGetId([
                    'group'       => '',
                    'title'       => $title,
                    'type'        => 'text',
                    'sort_order'  => 0,
                    'status'      => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }

            return $exist->id;
        }

        return 0;
    }


    /**
     * @param string $text
     *
     * @return string
     */
    private function replaceNames(string $text): string
    {
        $text = str_replace('Knji?evnost', 'Književnost', $text);
        $text = str_replace('Kazali?te', 'Kazalište', $text);
        $text = str_replace('knji?evnosti', 'književnosti', $text);

        return $text;
    }


    /**
     * @param string $name
     * @param int    $parent
     *
     * @return Category|null
     */
    private function findExistingCategory(string $name, int $parent = 0): ?Category
    {
        return Category::query()
                       ->where('title', trim($name))
                       ->where('parent_id', $parent)
                       ->orderBy('id')
                       ->first();
    }
}

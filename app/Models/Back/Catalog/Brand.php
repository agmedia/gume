<?php

namespace App\Models\Back\Catalog;

use App\Helpers\Helper;
use App\Models\Back\Catalog\Product\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'brands';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var Request
     */
    protected $request;


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    /**
     * Validate new category Request.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $this->request = $request;

        return $this;
    }


    /**
     * Store new category.
     *
     * @return false
     */
    public function create()
    {
        $slug = isset($this->request->slug) ? Str::slug($this->request->slug) : Str::slug($this->request->title);

        $id = $this->insertGetId([
            'letter'           => Helper::resolveFirstLetter($this->request->title),
            'title'            => $this->request->title,
            'description'      => $this->request->description,
            'meta_title'       => $this->request->meta_title,
            'meta_description' => $this->request->meta_description,
            'slug'             => $slug,
            'url'              => config('settings.brand_path') . '/' . $slug,
            'featured'         => (isset($this->request->featured) and $this->request->featured == 'on') ? 1 : 0,
            'sort_order'       => 0,
            'status'           => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'created_at'       => Carbon::now(),
            'updated_at'       => Carbon::now()
        ]);

        if ($id) {
            return $this->find($id);
        }

        return false;
    }


    /**
     * @param Category $category
     *
     * @return false
     */
    public function edit()
    {
        $slug = isset($this->request->slug) ? Str::slug($this->request->slug) : Str::slug($this->request->title);

        $id = $this->update([
            'letter'           => Helper::resolveFirstLetter($this->request->title),
            'title'            => $this->request->title,
            'description'      => $this->request->description,
            'meta_title'       => $this->request->meta_title,
            'meta_description' => $this->request->meta_description,
            'slug'             => $slug,
            'url'              => config('settings.brand_path') . '/' . $slug,
            'featured'         => (isset($this->request->featured) and $this->request->featured == 'on') ? 1 : 0,
            'sort_order'       => 0,
            'status'           => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'updated_at'       => Carbon::now()
        ]);

        if ($id) {
            return $this;
        }

        return false;
    }


    /**
     * @param Category $category
     *
     * @return bool
     */
    public function resolveImage(Brand $brand)
    {
        if ($this->request->hasFile('image')) {
            $name = Str::slug($brand->title) . '.' . $this->request->image->extension();

            $this->request->image->storeAs('/', $name, 'brand');

            return $brand->update([
                'image' => config('filesystems.disks.brand.url') . $name
            ]);
        }

        return false;
    }


    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    /**
     * @return int
     */
    public static function checkStatuses_CRON()
    {
        $log_start = microtime(true);

        $total = Brand::query()->pluck('id');

        $brands_with = Brand::query()->whereHas('products', function ($query) {
            $query->where('status', 1);
        })->pluck('id');

        $brands_without = $total->diff($brands_with);

        Brand::query()->whereIn('id', $brands_with)->update(['status' => 1]);
        Brand::query()->whereIn('id', $brands_without)->update(['status' => 0]);

        $log_end = microtime(true);
        Log::info('__Check Brand Statuses - Total Execution Time: ' . number_format(($log_end - $log_start), 2, ',', '.') . ' sec.');

        return 1;
    }
}

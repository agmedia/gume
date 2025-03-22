<?php

namespace App\Models\Front\Checkout;

use App\Models\Back\Orders\OrderProduct;
use App\Models\Back\Orders\OrderTotal;
use App\Models\Back\Settings\Settings;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    /**
     * @var string
     */
    protected $table = 'orders';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * @return mixed
     */
    public function getStatusAttribute()
    {
        return $this->status($this->order_status_id);
    }


    /**
     * @param int $id
     *
     * @return mixed
     */
    public function status(int $id)
    {
        $statuses = Settings::get('order', 'statuses');

        return $statuses->where('id', $id)->first();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id')->with('product');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totals()
    {
        return $this->hasMany(OrderTotal::class, 'order_id')->orderBy('sort_order');
    }


}

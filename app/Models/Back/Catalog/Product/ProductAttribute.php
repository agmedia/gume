<?php

namespace App\Models\Back\Catalog\Product;

use App\Models\Back\Catalog\Attributes\Attributes;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{

    /**
     * @var string $table
     */
    protected $table = 'product_attribute';

    /**
     * @var array $guarded
     */
    protected $guarded = [];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attribute()
    {
        return $this->hasOne(Attributes::class, 'id', 'attribute_id');
    }


    /**
     * @param array $attributes
     * @param int   $product_id
     *
     * @return array
     */
    public static function storeData(array $attributes, int $product_id): array
    {
        $created = [];
        self::where('product_id', $product_id)->delete();

        foreach ($attributes as $attribute) {
            $att = Attributes::find($attribute['id']);

            if ($att) {
                $created[] = self::insert([
                    'product_id'  => $product_id,
                    'attribute_id' => $attribute['id'],
                    'value'       => $attribute['value'],
                ]);
            }
        }

        return $created;
    }
}

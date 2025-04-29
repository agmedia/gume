<?php

namespace App\Models\Back\Settings\Api;

use App\Models\Back\Catalog\Product\Product;

class DataFeedWatch
{

    /**
     * @var string
     */
    protected $url = 'https://feeds.datafeedwatch.com/70335/d2bfb7399e3bee04d0dabb9b5f0954de960f8569.xml';

    /**
     * @var \$1|false|\SimpleXMLElement|null
     */
    protected $feed = null;


    /**
     * Class constructor.
     *
     * Initializes the feed property by loading XML from the provided URL
     * and triggers the update method to process the feed data.
     */
    public function __construct()
    {
        $this->feed = simplexml_load_file($this->url);
    }


    /**
     * Updates the prices and quantities of products based on the data from the feed.
     *
     * Iterates through the product data in the feed, searches for matching products in the database
     * using their SKU, and updates the price and quantity values. Returns the count of updated products.
     *
     * @return int The number of products successfully updated.
     */
    public function updatePricesAndQuantity(): int
    {
        $count = 0;

        if ($this->feed) {
            foreach ($this->feed->product as $item) {
                $product = Product::query()->where('sku', (string) $item->product_code)->first();

                if ($product) {
                    $product->update([
                        'price' => str_replace(' EUR', '', (string) $item->price),
                        'quantity' => (string) $item->stock_number,
                    ]);

                    $count++;
                }
            }
        }

        return $count;
    }
}
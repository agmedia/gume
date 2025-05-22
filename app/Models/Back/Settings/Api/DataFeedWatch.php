<?php

namespace App\Models\Back\Settings\Api;

use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Settings\Temp;

class DataFeedWatch
{

    /**
     * @var string
     */
    protected $url = 'https://feeds.datafeedwatch.com/70335/d2bfb7399e3bee04d0dabb9b5f0954de960f8569.xml';

    /**
     * @var string
     */
    protected $url_2 = 'https://feeds.datafeedwatch.com/70335/d8aa73ceb924b75fd493399154b0c61f3ec93178.xml';

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
    public function updatePricesAndQuantity(bool $second_feeds = true): int
    {
        $count = 0;
        $update_arr = collect();

        if ($this->feed) {
            foreach ($this->feed->product as $item) {
                $product = Product::query()->where('sku', (string) $item->product_code)->first();

                if ($product) {
                    $product->update([
                        'price' => str_replace(' EUR', '', (string) $item->price),
                        'quantity' => (string) $item->stock_number,
                    ]);

                    $count++;
                    
                    $has = Temp::query()->where('sku', (string) $item->product_code)->first();
                    
                    if ( ! $has) {
                        $has_in = $update_arr->where('sku', (string) $item->product_code)->first();
                        
                        if ( ! $has_in) {
                            $update_arr->push([
                                'sku' => (string) $item->product_code,
                                'quantity' => (string) $item->stock_number
                            ]);
                        }
                    }
                }
            }
            
            Temp::query()->insert($update_arr->toArray());
        }

        if ($second_feeds) {
            $count_2 = $this->checkSecondUrl();
        }

        return $count + $count_2;
    }


    /**
     * Checks the second URL for a feed and updates product prices and quantities if available.
     *
     * Verifies if the second URL is not empty, attempts to load the feed data from the URL,
     * and updates the products by invoking the `updatePricesAndQuantity` method. Returns the count
     * of successfully updated products. If the second URL is empty, no updates are performed.
     *
     * @return int The number of products successfully updated or 0 if the second URL is empty.
     */
    public function checkSecondUrl(): int
    {
        if ($this->url_2 != '') {
            $this->feed = simplexml_load_file($this->url_2);

            return $this->updatePricesAndQuantity(false);
        }

        return 0;
    }
}

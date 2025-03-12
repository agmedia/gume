<?php

namespace App\Helpers;

use App\Models\Back\Catalog\Product\ProductCategory;
use App\Models\Front\Catalog\ProductAction;
use App\Models\Front\Catalog\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class Special
{

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected $user;

    /**
     * @var int
     */
    protected $user_group;

    /**
     * @var Product|null
     */
    protected $product;

    /**
     * @var float|int|null
     */
    protected $product_option_price;

    /**
     * @var ProductAction|null
     */
    protected $action;

    /**
     * @var ProductAction|null
     */
    protected $user_group_action;

    /**
     * @var Collection|null
     */
    private $active_actions;


    /**
     * @param Product|null       $product
     * @param ProductAction|null $product_action
     */
    public function __construct(Product $product = null, float|null $option_price = null)
    {
        $this->user    = Auth::user();
        $this->product = $product;
        $this->product_option_price = $option_price ?: 0;

        if ($this->user) {
            $this->user_group = $this->user->details->group ? $this->user->details->group->id : 0;
        }
    }


    /**
     * @return mixed
     */
    public function getUserGroupAction(): ?ProductAction
    {
        return $this->user_group_action;
    }


    /**
     * @return ProductAction|null
     */
    public function getAction(): ?ProductAction
    {
        return $this->action;
    }


    /**
     * @return ProductAction|null
     */
    public function resolveAction(): ?ProductAction
    {
        if (Auth::check() && $this->userHasGroupDiscount()) {
            return $this->getUserGroupAction();
        }

        return $this->setupAvailableActions()->getAction();
    }


    /**
     * @return bool
     */
    public function userHasGroupDiscount(): bool
    {
        if ($this->user_group) {
            $this->active_actions = ProductAction::query()->where('user_group_id', $this->user_group)->active()->get();

            if ($this->active_actions->count()) {
                $this->user_group_action = $this->getBestAction();

                return true;
            }
        }

        $this->active_actions = ProductAction::query()->where('user_group_id', null)->active()->get();

        if ($this->active_actions->count()) {
            $this->action = $this->getBestAction();
        }

        return false;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return float|int
     */
    public function getDiscountPrice(ProductAction|null $product_action = null): float|int
    {
        $action = $this->resolveRealAction($product_action);
        $product_price = $this->product->price + $this->product_option_price;

        if ( ! $action) {
            return $product_price;
        }

        if ($this->isProductOnAction($action)) {
            return Helper::calculateDiscountPrice($product_price, $action->discount, $action->type);

        } else {
            return $product_price;
        }
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return bool
     */
    public function checkCoupon(ProductAction|null $product_action = null): bool
    {
        $action             = $this->resolveRealAction($product_action);
        $coupon_session_key = config('session.cart') . '_coupon';
        $coupon             = false;

        if ( ! $action || ($action && ! $action->coupon)) {
            $coupon = true;
        }

        if (isset($action->status) && $action->status) {
            if ((isset($action->coupon) && $action->coupon) && session()->has($coupon_session_key) && session($coupon_session_key) == $action->coupon) {
                $coupon = true;
            }
        }

        return $coupon;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return bool
     */
    public function checkDates(ProductAction|null $product_action = null): bool
    {
        $action = $this->resolveRealAction($product_action);

        if ( ! $action) {
            return false;
        }

        $from = now()->subDay();
        $to   = now()->addDay();

        if ($action->date_start && $action->date_start != '0000-00-00 00:00:00') {
            $from = Carbon::make($action->date_start);
        }
        if ($action->date_end && $action->date_end != '0000-00-00 00:00:00') {
            $to = Carbon::make($action->date_end);
        }

        if ($from <= now() && now() <= $to) {
            return true;
        }

        return false;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return bool
     */
    public function isProductOnAction(ProductAction|null $product_action = null): bool
    {
        $action = $this->resolveRealAction($product_action);

        if ($this->isActionOnAllProducts($action)) {
            return true;
        }

        $ids = $this->getActionProductsList($action);

        if (in_array($this->product->id, $ids)) {
            return true;
        }

        return false;
    }


    /**
     * @return bool
     */
    public function isActionOnAllProducts(ProductAction|null $product_action = null): bool
    {
        if (in_array($product_action->group, ['all', 'total'])) {
            return true;
        }

        return false;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return array
     */
    public function getActionProductsList(ProductAction|null $product_action = null): array
    {
        $action = $this->resolveRealAction($product_action);

        $ids = explode(',', substr(str_replace('"', '', $action->links), 1, -1));

        if ($action->group == 'product') {
            return $ids;
        }

        if ($action->group == 'category') {
            return ProductCategory::query()->whereIn('category_id', $ids)->pluck('product_id')->unique()->toArray();
        }

        if ($action->group == 'brand') {
            return Product::query()->whereIn('brand_id', $ids)->pluck('id')->unique()->toArray();
        }

        return [];
    }


    /**
     * @return mixed
     */
    public function getBestAction()
    {
        if ($this->active_actions->count() > 1) {
            $price          = $this->product->price + $this->product_option_price;
            $best_action_id = 0;

            foreach ($this->active_actions as $action) {
                $coupon_ok = $this->checkCoupon($action);
                $dates_ok  = $this->checkDates($action);

                if ($coupon_ok && $dates_ok) {
                    $temp_price = $this->getDiscountPrice($action);

                    if ($price > $temp_price) {
                        $price          = $temp_price;
                        $best_action_id = $action->id;
                    }
                }
            }

            return $this->active_actions->where('id', $best_action_id)->first();
        }

        return $this->active_actions->first();
    }


    /**
     * @return Special
     */
    private function setupAvailableActions(): Special
    {
        $this->active_actions = ProductAction::query()->where('user_group_id', null)->active()->get();

        if ($this->active_actions->count()) {
            $this->action = $this->getBestAction();
        }

        return $this;
    }


    /**
     * @param ProductAction|null $product_action
     *
     * @return ProductAction|null
     */
    private function resolveRealAction(ProductAction|null $product_action = null)
    {
        if ($product_action) {
            return $product_action;
        }

        if ($this->user_group) {
            if ($this->user_group_action) {
                return $this->user_group_action;
            }
        }

        return $this->action;
    }
}

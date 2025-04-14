<?php

namespace App\Models\Back\Hotel;

use App\Models\Back\Catalog\Brand;
use App\Models\Back\Settings\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Bouncer;
use Illuminate\Support\Facades\Log;

class Hotel extends Model
{

    /**
     * @var string
     */
    protected $table = 'hotel';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var string[]
     */
    protected $appends = ['username', 'status'];

    /**
     * @var Request
     */
    protected $request;


    /**
     * @return mixed
     */
    public function getStatusAttribute()
    {
        return $this->getStatus($this->status_id);
    }


    /**
     * @param $value
     *
     * @return string
     */
    public function getUsernameAttribute($value)
    {
        return $this->user->name;
    }


    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getStatus(int $id)
    {
        $statuses = Settings::get('order', 'statuses');

        return $statuses->where('id', $id)->first();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }


    /**
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'user_id'  => 'required'
        ]);

        $this->setRequest($request);

        return $this;
    }


    /**
     * Create and return new Product Model.
     *
     * @return mixed
     */
    public function create()
    {
        $id = $this->insertGetId($this->getModelArray());

        if ($id) {
            return $this->find($id);
        }

        return false;
    }


    /**
     * Update and return new Product Model.
     *
     * @return mixed
     */
    public function edit()
    {
        $updated = $this->update($this->getModelArray(false));

        if ($updated) {
            return $this;
        }

        return false;
    }


    /**
     * @param bool $insert
     *
     * @return array
     */
    private function getModelArray(bool $insert = true): array
    {
        $response = [
            'user_id'      => $this->request->user_id,
            'status_id'    => $this->request->reservation_status ?: 3,
            'brand_id'     => $this->request->brand_id,
            'invoice'      => $this->request->invoice,
            'dimension'    => $this->request->dimension,
            'type'         => $this->request->type,
            'quantity'     => $this->request->quantity,
            'reg'          => $this->request->reg,
            'start_date'   => Carbon::make($this->request->start_date),
            'end_date'     => $this->request->end_date ? Carbon::make($this->request->end_date) : Carbon::make($this->request->start_date)->addMonths(6),
            'message'      => $this->request->message,
            'condition_lp' => $this->request->condition_lp,
            'condition_dp' => $this->request->condition_dp,
            'condition_lz' => $this->request->condition_lz,
            'condition_dz' => $this->request->condition_dz,
            'comment'      => $this->request->message,
            'paid'         => 0,
            'active'       => (isset($this->request->active) and $this->request->active == 'on') ? 1 : 0,
            'updated_at'   => now()
        ];

        if ($insert) {
            $response['created_at'] = now();
        }

        return $response;
    }


    /**
     * Set Model request variable.
     *
     * @param $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }


    /**
     * @param Request $request
     *
     * @return Builder
     */
    public function filter(Request $request): Builder
    {
        $query = $this->newQuery();

        if ($request->has('status')) {
            $query->where('status_id', '=', $request->input('status'));
        }

        if ($request->has('search') && ! empty($request->input('search'))) {
            $query->where(function ($query) use ($request) {
                return $query->where('id', 'like', '%' . $request->input('search') . '%')
                             ->orWhere('invoice', 'like', '%' . $request->input('search') . '%')
                             ->orWhere('reg', 'like', '%' . $request->input('search') . '%')
                             ->orWhereHas('user', function ($query) use ($request) {
                                 $query->where('email', 'like', '%' . $request->input('search') . '%');
                             });
            });
        }

        return $query->orderBy('created_at', 'desc')
                     ->with('user');
    }


    /**
     * @return string[]
     */
    public static function conditionSelectList(): array
    {
        return ['Odlično', 'Srednje', 'Loše/oštećeno'];
    }

}

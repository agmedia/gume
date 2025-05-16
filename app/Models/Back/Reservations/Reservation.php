<?php

namespace App\Models\Back\Reservations;

use App\Models\Back\Orders\Order;
use App\Models\Back\Settings\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Bouncer;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class Reservation extends Model
{

    /**
     * @var string
     */
    protected $table = 'reservations';

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
     * @param $value
     *
     * @return string
     */
    public function getUsernameAttribute($value)
    {
        if ($this->order_id) {
            return $this->order->payment_fname . ' ' . $this->order->payment_lname;
        }

        if ($this->user) {
            return $this->user->name;
        }

        return 'Korisnik nije upisan';
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    /**
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'user_id'          => 'required',
            'reservation_date' => 'required',
            'time'             => 'required'
        ]);

        $this->setRequest($request);

        return $this;
    }


    /**
     * Create and return new Reservation Model.
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
     * Update and return new Reservation Model.
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
        $date = Carbon::make($this->request->reservation_date);

        $response = [
            'order_id'         => $this->request->order_id,
            'user_id'          => $this->request->user_id,
            'status_id'        => $this->request->reservation_status ?: 3,
            'reservation_date' => $date,
            'day'              => $date->day,
            'month'            => $date->month,
            'year'             => $date->year,
            'time'             => $this->request->time,
            'message'          => $this->request->message,
            'active'           => (isset($this->request->active) and $this->request->active == 'on') ? 1 : 0,
            'updated_at'       => now()
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
                             ->orWhereHas('order', function ($query) use ($request) {
                                 $query->where('payment_fname', 'like', '%' . $request->input('search') . '%')
                                       ->orWhere('payment_lname', 'like', '%' . $request->input('search') . '%')
                                       ->orWhere('payment_email', 'like', '%' . $request->input('search') . '%');
                             })
                             ->orWhereHas('user', function ($query) use ($request) {
                                 $query->where('email', 'like', '%' . $request->input('search') . '%');
                             });
            });
        }

        return $query->orderBy('reservation_date', 'DESC')
                     ->orderBy('time', 'ASC')
                     ->with(['order', 'user']);
    }

}

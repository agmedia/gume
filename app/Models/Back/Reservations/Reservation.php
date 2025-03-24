<?php

namespace App\Models\Back\Reservations;

use App\Models\Back\Orders\Order;
use App\Models\Back\Settings\Settings;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Bouncer;
use Illuminate\Support\Facades\Log;

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
     * @var Request
     */
    protected $request;


    /**
     * @return mixed
     */
   /* public function getStatusAttribute()
    {
        return $this->status($this->order_status_id);
    }*/


    /**
     * @param int $id
     *
     * @return mixed
     */
    /*public function status(int $id)
    {
        $statuses = Settings::get('order', 'statuses');

        return $statuses->where('id', $id)->first();
    }*/


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }


    /**
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'order_id'         => 'required',
            'reservation_date' => 'required',
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
        $date = Carbon::make($this->request->reservation_date);

        $response = [
            'order_id'         => $this->request->order_id,
            'status_id'        => 1,
            'reservation_date' => $date,
            'day'              => $date->day,
            'month'            => $date->month,
            'year'             => $date->year,
            'time'             => $this->request->time,
            'message'          => $this->request->message,
            'status'           => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
            'updated_at'       => now()
        ];

        if ($insert) {
            $response['created_at'] = now();
        }

        return $response;
    }


    private function resolveDateFields()
    {
        
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
        $query = $this->newQuery()->with('order');

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
                             });
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

}

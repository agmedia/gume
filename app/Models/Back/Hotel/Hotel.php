<?php

namespace App\Models\Back\Hotel;

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
     * @var Request
     */
    protected $request;


    /**
     * @return mixed
     */
    public function getStatusAttribute()
    {
        return $this->resolveStatus($this->order_status_id);
    }


    /**
     * @param int $id
     *
     * @return mixed
     */
    public function resolveStatus(int $id)
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
     * @param Request $request
     *
     * @return $this
     */
    public function validateRequest(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'phone' => 'required',
            'email' => 'required|email',
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
            'status_id'    => 1,
            'invoice'      => $this->request->invoice,
            'name'         => $this->request->name,
            'phone'        => $this->request->phone,
            'email'        => $this->request->email,
            'brand'        => $this->request->brand,
            'dimension'    => $this->request->dimension,
            'type'         => $this->request->type,
            'quantity'     => $this->request->quantity,
            'reg'          => $this->request->reg,
            'start_date'   => $this->request->start_date ? Carbon::make($this->request->start_date) : null,
            'end_date'     => $this->request->end_date ? Carbon::make($this->request->end_date) : null,
            'message'      => $this->request->message,
            'condition_lp' => $this->request->condition_lp,
            'condition_dp' => $this->request->condition_dp,
            'condition_lz' => $this->request->condition_lz,
            'condition_dz' => $this->request->condition_dz,
            'comment'      => $this->request->comment,
            'paid'         => $this->request->paid,
            'status'       => (isset($this->request->status) and $this->request->status == 'on') ? 1 : 0,
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
        $query = $this->newQuery()->with('order');

        if ($request->has('status')) {
            $query->where('status_id', '=', $request->input('status'));
        }

        if ($request->has('search') && ! empty($request->input('search'))) {
            $query->where(function ($query) use ($request) {
                return $query->where('id', 'like', '%' . $request->input('search') . '%')
                             ->orWhere('name', 'like', '%' . $request->input('search') . '%')
                             ->orWhere('phone', 'like', '%' . $request->input('search') . '%')
                             ->orWhere('email', 'like', '%' . $request->input('search') . '%')
                             ->orWhereHas('user', function ($query) use ($request) {
                                 $query->where('name', 'like', '%' . $request->input('search') . '%');
                             });
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

}

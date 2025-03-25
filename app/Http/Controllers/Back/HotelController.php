<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Hotel\Hotel;
use App\Models\Back\Orders\Order;
use App\Models\Back\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HotelController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Hotel $hotel)
    {
        $hotels   = $hotel->filter($request)->paginate(config('settings.pagination.back'));
        $statuses = $this->settings()->all();

        return view('back.hotel.index', compact('hotels', 'statuses'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $statuses = $this->settings()->pluck('title', 'id')->all();
        $conditions = Hotel::conditionSelectList();

        return view('back.hotel.edit', compact('statuses', 'conditions'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $hotel = new Hotel();

        $stored = $hotel->validateRequest($request)->create();

        if ($stored) {
            return redirect()->route('hotels.edit', ['hotel' => $stored])->with(['success' => 'Upis je snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Oops..! Dogodila se greška prilikom snimanja.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Hotel $hotel)
    {
        $statuses = $this->settings()->pluck('title', 'id')->all();

        return view('back.hotel.show', compact('hotel', 'statuses'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Hotel $hotel)
    {
        $statuses = $this->settings()->pluck('title', 'id')->all();
        $conditions = Hotel::conditionSelectList();

        return view('back.hotel.edit', compact('hotel', 'statuses', 'conditions'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Order                    $order
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hotel $hotel)
    {
        $updated = $hotel->validateRequest($request)->edit();

        if ($updated) {
            return redirect()->route('hotels.edit', ['hotel' => $updated])->with(['success' => 'Upis je snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Oops..! Dogodila se greška prilikom snimanja.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {}

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    private function settings()
    {
        return Settings::get('order', 'statuses')
                       ->whereIn('id', config('settings.reservation_statuses'));
    }

}

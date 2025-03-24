<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Reservations\Reservation;
use App\Models\Back\Settings\Settings;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Reservation $reservation)
    {
        $reservations   = $reservation->filter($request)->paginate(config('settings.pagination.back'));


        $statuses = Settings::get('order', 'statuses');

        return view('back.reservation.index', compact('reservations', 'statuses'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('back.reservation.edit');
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
        $reservation = new Reservation();

        $stored = $reservation->validateRequest($request)->create();

        if ($stored) {
            return redirect()->route('reservation.edit', ['reservation' => $stored])->with(['success' => 'Rezervacija je snimljena!']);
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
    public function show(Reservation $reservation)
    {
        $statuses = Settings::get('order', 'statuses');

        return view('back.reservation.show', compact('reservation', 'statuses'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        $statuses = Settings::get('order', 'statuses');

        return view('back.reservation.edit', compact('reservation', 'statuses'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Order                    $order
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $reservation)
    {
        $updated = $reservation->validateRequest($request)->edit();

        if ($updated) {
            return redirect()->route('reservations.edit', ['reservation' => $updated])->with(['success' => 'Rezervacija je snimljena!']);
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

}

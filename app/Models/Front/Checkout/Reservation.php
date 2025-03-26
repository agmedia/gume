<?php

namespace App\Models\Front\Checkout;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
     * @param int $range
     *
     * @return array
     */
    public static function getUpcomingDays(int $range = 14): array
    {
        $days = [];
        $items = CarbonPeriod::create(now(), now()->addDays(14));

        foreach ($items as $day) {
            array_push($days, [
                'date' => $day->format('Y-m-d'),
                'day' => $day->format('d'),
                'title' => $day->locale('hr')->translatedFormat('D'),
            ]);
        }

        return $days;
    }


    /**
     * @return array
     */
    public static function getHoursList(string $day = null): array
    {
        $day = Carbon::parse($day);

        $from = Carbon::parse('08:00');
        $to = Carbon::parse('17:00');

        if ($day->isSunday()) {
            return [];
        }

        if ($day->isSaturday()) {
            $to = Carbon::parse('12:00');
        }

        $hours = [];
        $items = CarbonPeriod::create($from, '30 minutes', $to);

        foreach ($items as $hour) {
            array_push($hours, [
                'from' => $hour->format('H:i'),
                'to' => $hour->addMinutes(30)->format('H:i'),
                'available' => 1
            ]);
        }

        for ($i = 0; $i < count($hours); $i++) {
            $reservation = Reservation::query()->where('reservation_date', '=', $day->format('Y-m-d'))
                                      ->where('time', '=', $hours[$i]['from'] . ' - ' . $hours[$i]['to'])
                                      ->exists();

            $hours[$i]['available'] = $reservation ? 0 : 1;
        }

        return $hours;
    }

}

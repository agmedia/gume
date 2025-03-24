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
                'title' => $day->locale('hr')->format('D'),
            ]);
        }

        return $days;
    }


    /**
     * @return array
     */
    public static function getHoursList(): array
    {
        $hours = [];
        $items = CarbonPeriod::create(Carbon::parse('08:00'), '1 hour', Carbon::parse('15:00'));

        foreach ($items as $hour) {
            array_push($hours, [
                'from' => $hour->format('H:i'),
                'to' => $hour->addHour()->format('H:i'),
            ]);
        }

        return $hours;
    }

}

<?php

namespace App\Models\Front\Checkout;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Reservation extends Model
{

    public const MIN_BOOKING_DAYS = 3;

    public const BOOKING_RANGE_DAYS = 14;

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
    public static function getUpcomingDays(int $range = self::BOOKING_RANGE_DAYS): array
    {
        $days  = [];
        $start = self::earliestBookingDate();
        $items = CarbonPeriod::create($start, $start->copy()->addDays(max(0, $range)));

        foreach ($items as $day) {
            array_push($days, [
                'date'  => $day->format('Y-m-d'),
                'day'   => $day->format('d'),
                'title' => $day->locale('hr')->translatedFormat('D'),
            ]);
        }

        return $days;
    }


    /**
     * @return Carbon
     */
    public static function earliestBookingDate(): Carbon
    {
        return today()->addDays(self::MIN_BOOKING_DAYS);
    }


    /**
     * @param string|null $day
     * @param int         $range
     *
     * @return bool
     */
    public static function isBookableDay(?string $day, int $range = self::BOOKING_RANGE_DAYS): bool
    {
        if (empty($day)) {
            return false;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $day)->startOfDay();
        } catch (\Throwable $exception) {
            return false;
        }

        if ($date->format('Y-m-d') !== $day) {
            return false;
        }

        $earliest = self::earliestBookingDate();
        $latest   = $earliest->copy()->addDays(max(0, $range));

        return $date->betweenIncluded($earliest, $latest);
    }


    /**
     * @return array
     */
    public static function getHoursList(?string $day = null): array
    {
        if ( ! self::isBookableDay($day)) {
            return [];
        }

        $day = Carbon::parse($day);

        $from = Carbon::parse('08:00');
        $to   = Carbon::parse('17:00');

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
                'from'      => $hour->format('H:i'),
                'to'        => $hour->addMinutes(30)->format('H:i'),
                'available' => 1
            ]);
        }

        for ($i = 0; $i < count($hours); $i++) {
            $reservation = Reservation::query()->where('reservation_date', '=', $day->format('Y-m-d'))
                                               ->where('status_id', '!=', 5)
                                               ->where(function (Builder $query) use ($hours, $i) {
                                                   $query->where('time', '=', $hours[$i]['from'] . ' - ' . $hours[$i]['to'])
                                                         ->orWhere('time', '=', $hours[$i]['from'] . '-' . $hours[$i]['to']);
                                               })
                                               ->exists();

            $hours[$i]['available'] = $reservation ? 0 : 1;
        }

        return $hours;
    }


    /**
     * @param string|null $day
     * @param string|null $time
     *
     * @return bool
     */
    public static function isSlotAvailable(?string $day, ?string $time): bool
    {
        if (empty($time)) {
            return false;
        }

        foreach (self::getHoursList($day) as $hour) {
            $compactTime = $hour['from'] . '-' . $hour['to'];
            $spacedTime  = $hour['from'] . ' - ' . $hour['to'];

            if ($hour['available'] && in_array($time, [$compactTime, $spacedTime], true)) {
                return true;
            }
        }

        return false;
    }

}

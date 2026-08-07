<?php

namespace Tests\Feature;

use App\Models\Front\Checkout\Reservation;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReservationLeadTimeTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_first_booking_day_is_three_days_from_today(): void
    {
        Carbon::setTestNow('2026-08-07 10:00:00');

        $days = Reservation::getUpcomingDays();

        $this->assertSame('2026-08-10', $days[0]['date']);
        $this->assertTrue(Reservation::isBookableDay('2026-08-10'));
    }

    public function test_earlier_booking_days_and_hours_are_rejected(): void
    {
        Carbon::setTestNow('2026-08-07 10:00:00');

        $this->assertFalse(Reservation::isBookableDay('2026-08-09'));
        $this->assertSame([], Reservation::getHoursList('2026-08-09'));
    }
}

<?php

namespace Tests\Feature;

use App\Services\ReservationService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReservationValidationTest extends TestCase
{
    public function test_valid_time_slots(): void
    {
        $service = new ReservationService;

        // 1 hour slot (16:30 to 17:30) - the user's scenario
        $service->validateTimeSlot('16:30', '17:30');

        // 30 min slot (08:00 to 08:30)
        $service->validateTimeSlot('08:00', '08:30');

        // Multi-hour slot (09:00 to 12:30)
        $service->validateTimeSlot('09:00', '12:30');

        // Max range slot (07:00 to 20:00)
        $service->validateTimeSlot('07:00', '20:00');

        $this->assertTrue(true);
    }

    public function test_invalid_duration_less_than_30_mins(): void
    {
        $service = new ReservationService;

        $this->expectException(ValidationException::class);
        $service->validateTimeSlot('08:00', '08:00');
    }

    public function test_end_before_start(): void
    {
        $service = new ReservationService;

        $this->expectException(ValidationException::class);
        $service->validateTimeSlot('14:00', '13:00');
    }

    public function test_outside_operating_hours(): void
    {
        $service = new ReservationService;

        $this->expectException(ValidationException::class);
        $service->validateTimeSlot('06:30', '07:30');
    }
}

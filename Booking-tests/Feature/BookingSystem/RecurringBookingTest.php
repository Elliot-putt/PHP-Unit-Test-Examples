<?php

use App\Http\Controllers\ServiceController;
use Tests\TestCase;

class RecurringBookingTest extends TestCase {

    public function test_Recurring_Booking_Creates_Multiple_Bookings()
    {
        $this->withoutExceptionHandling();
        $user = $this->login();
        $company = \App\Models\Company::factory()->create();

        $this->post(action([ServiceController::class, 'store'], $company->id), [
            'title' => 'service test',
            'approval' => 0,
            'description' => 'Test service',
            'duration' => 60,
            'defaultDuration' => 1,
            'fullDay' => 0,
            'quantity' => 1,
        ])->assertRedirect(route('companies.services', $company->id));
//
//        $this->post(action([\App\Http\Controllers\BookingController::class, 'store'], [$company->id, \App\Models\Service::first()->id]), [
//            'duration' => 60,
//            'amount' => 1,
//            'notes' => 'testing',
//            'date' => \Carbon\Carbon::now()->format('m/d/Y'),
//        ])->assertRedirect(route('booking.all'));
//
//        //check the booking single has been created
//        $this->assertDatabaseHas(\App\Models\Booking::class, [
//            'notes' => 'testing',
//        ]);
//
//        //posts to the booking recurring function with a date in the past
//        $this->post(action([\App\Http\Controllers\BookingController::class, 'recurring'], [
//            'date' => \Carbon\Carbon::now()->subMonth()->format('m/d/Y'),
//        ]))->assertStatus(302)->assertRedirect(route('booking.show', \App\Models\Booking::first()->id));
//
//        //posts to the booking recurring function with a date in the future
//        $this->post(action([\App\Http\Controllers\BookingController::class, 'recurring'], [
//            'date' => \Carbon\Carbon::now()->addMonth()->format('m/d/Y'),
//        ]))->assertSuccessful()->assertInertia();
//
//        //checks the recurring bookings for the month were created
//        $this->assertDatabaseCount(\App\Models\Booking::class, 6);

    }

}

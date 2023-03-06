<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BookingPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Guest_Users_Are_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->guest()->create());
        //Model trying to be accessed
        $booking = \App\Models\Booking::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $booking);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Staff_Users_Are_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->staff()->create());
        //assigning the logged-in user to the company

        $booking = \App\Models\Booking::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $booking);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Parent_Users_Are_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->parent()->create());
        //Model trying to be accessed
        $booking = \App\Models\Booking::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $booking);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->global()->create());
        // model trying to be access
        $booking = \App\Models\Booking::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $booking);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Booking $booking) => $this->get(action([\App\Http\Controllers\BookingController::class, 'all']))];
        yield [fn(\App\Models\Booking $booking) => $this->get(action([\App\Http\Controllers\BookingController::class, 'show'], $booking->id))];
    }

}

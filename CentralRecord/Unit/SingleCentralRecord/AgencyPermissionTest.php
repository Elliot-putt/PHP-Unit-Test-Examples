<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AgencyPermissionTest extends TestCase {


    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_Agency_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $agency = \App\Models\Agency::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $agency);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Manager_Users_Are_Allowed_Agency_Access(Closure $sendRequest)
    {
        $manager = $this->login(User::factory()->manager()->create());
        $agency = \App\Models\Agency::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $agency);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_Agency_Access(Closure $sendRequest)
    {

        $global = $this->login();
        $agency = \App\Models\Agency::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $agency);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Agency $agency) => $this->get(action([\App\Http\Controllers\AgencyController::class, 'index']))];
        yield [fn(\App\Models\Agency $agency) => $this->get(action([\App\Http\Controllers\AgencyController::class, 'create']))];
        yield [fn(\App\Models\Agency $agency) => $this->get(action([\App\Http\Controllers\AgencyController::class, 'show'], $agency->id))];
        yield [fn(\App\Models\Agency $agency) => $this->post(action([\App\Http\Controllers\AgencyController::class, 'store']))];
        yield [fn(\App\Models\Agency $agency) => $this->patch(action([\App\Http\Controllers\AgencyController::class, 'update'], $agency->id))];
        yield [fn(\App\Models\Agency $agency) => $this->delete(action([\App\Http\Controllers\AgencyController::class, 'destroy'], $agency->id))];


    }

}

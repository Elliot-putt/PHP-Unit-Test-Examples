<?php

namespace Tests\Unit\SingleCentralRecord;

use App\Jobs\RoleBoot;
use App\Models\Location;
use App\Models\LocationUser;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Closure;
use function action;

class LocationPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_Location_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $location = \App\Models\Location::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $location);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Managers_Users_Are_Allowed_Location_Access(Closure $sendRequest)
    {


        $manager = $this->login(User::factory()->manager()->create());
        $location = \App\Models\Location::factory()->create();
        $this->assignLocationsRelationship($location->toArray() , $manager);

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $location);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_Location_Access(Closure $sendRequest)
    {


        $global = $this->login();
        $location = \App\Models\Location::factory()->create();
        $this->assignLocationsRelationship($location->toArray() , $global);

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $location);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Location $location) => $this->get(action([\App\Http\Controllers\LocationController::class, 'index']))];
        yield [fn(\App\Models\Location $location) => $this->get(action([\App\Http\Controllers\LocationController::class, 'create']))];
        yield [fn(\App\Models\Location $location) => $this->get(action([\App\Http\Controllers\LocationController::class, 'show'], $location->id))];
        yield [fn(\App\Models\Location $location) => $this->post(action([\App\Http\Controllers\LocationController::class, 'store']))];
        yield [fn(\App\Models\Location $location) => $this->patch(action([\App\Http\Controllers\LocationController::class, 'update'], $location->id))];
        yield [fn(\App\Models\Location $location) => $this->delete(action([\App\Http\Controllers\LocationController::class, 'destroy'], $location->id))];


    }

}

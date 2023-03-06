<?php

namespace Tests\Unit\SingleCentralRecord;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Closure;
use function action;

class StaffTypePermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_StaffType_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $staffType = \App\Models\StaffType::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $staffType);

        //expected Response
        $response->assertForbidden();
    }
    /**
     * @test
     * @dataProvider requests
     */
    public function test_Manager_Users_Are_Allowed_StaffType_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->manager()->create());

        //Model trying to be accessed
        $staffType = \App\Models\StaffType::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $staffType);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }


    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_StaffType_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->global()->create());

        //Model trying to be accessed
        $staffType = \App\Models\StaffType::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $staffType);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }


    public function requests(): \Generator
    {
        yield [fn(\App\Models\StaffType $StaffType) => $this->post(action([\App\Http\Controllers\StaffTypeController::class, 'store']))];
        yield [fn(\App\Models\StaffType $StaffType) => $this->put(action([\App\Http\Controllers\StaffTypeController::class, 'update'], $StaffType->id))];
        yield [fn(\App\Models\StaffType $StaffType) => $this->post(action([\App\Http\Controllers\StaffTypeController::class, 'delete'], $StaffType->id))];

    }

}

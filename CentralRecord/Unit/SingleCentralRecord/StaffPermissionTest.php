<?php

namespace Tests\Unit\SingleCentralRecord;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Closure;
use function action;

class StaffPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_Staff_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $staff = \App\Models\Staff::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $staff);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Manager_Users_Are_Allowed_Staff_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $manager = $this->login(User::factory()->manager()->create());
        //creating and assigning user locations
        $location = \App\Models\Location::factory()->create();
        $this->assignLocationsRelationship($location->toArray(), $manager);
        //Model trying to be accessed
        $staff = \App\Models\Staff::factory()->create();
        $staffType = \App\Models\StaffType::factory()->create();
        $locationStaff = \App\Models\LocationStaff::create([
            'staff_id' => $staff->id,
            'staff_type_id' => $staffType->id,
            'location_id' => $location->id,
        ]);
        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $staff);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_Staff_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $global = $this->login(User::factory()->global()->create());
        //creating and assigning user locations
        $location = \App\Models\Location::factory()->create();
        $this->assignLocationsRelationship($location->toArray(), $global);
        //Model trying to be accessed
        $staff = \App\Models\Staff::factory()->create();
        $staff = \App\Models\Staff::factory()->create();
        $staffType = \App\Models\StaffType::factory()->create();
        $locationStaff = \App\Models\LocationStaff::create([
            'staff_id' => $staff->id,
            'staff_type_id' => $staffType->id,
            'location_id' => $location->id,
        ]);
        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $staff);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Staff $staff) => $this->get(action([\App\Http\Controllers\StaffController::class, 'index']))];
        yield [fn(\App\Models\Staff $staff) => $this->get(action([\App\Http\Controllers\StaffController::class, 'create']))];
        yield [fn(\App\Models\Staff $staff) => $this->get(action([\App\Http\Controllers\StaffController::class, 'show'], $staff->id))];
        yield [fn(\App\Models\Staff $staff) => $this->post(action([\App\Http\Controllers\StaffController::class, 'store']))];
        yield [fn(\App\Models\Staff $staff) => $this->patch(action([\App\Http\Controllers\StaffController::class, 'update'], $staff->id))];
        yield [fn(\App\Models\Staff $staff) => $this->delete(action([\App\Http\Controllers\StaffController::class, 'destroy'], $staff->id))];
    }

}

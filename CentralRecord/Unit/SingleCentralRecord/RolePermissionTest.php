<?php

namespace Tests\Unit\SingleCentralRecord;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Closure;
use function action;

class RolePermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_Role_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $role = \App\Models\Role::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $role);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Managers_Users_Are_Allowed_Role_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $manager = $this->login(User::factory()->manager()->create());

        //Model trying to be accessed
        $role = \App\Models\Role::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $role);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_Role_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->global()->create());

        //Model trying to be accessed
        $role = \App\Models\Role::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $role);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Role $role) => $this->post(action([\App\Http\Controllers\RoleController::class, 'store']))];
        yield [fn(\App\Models\Role $role) => $this->post(action([\App\Http\Controllers\RoleController::class, 'roleSync']))];
        yield [fn(\App\Models\Role $role) => $this->delete(action([\App\Http\Controllers\RoleController::class, 'destroy'], $role->id))];


    }

}

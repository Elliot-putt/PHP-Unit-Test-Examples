<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;
use \App\Models\Role;

class RolePermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Guest_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->guest()->create());
        //Model trying to be accessed
        $role = Role::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $role);

        //expected Response
        $response->assertInertia(fn(AssertableInertia $inertia) => $inertia
            ->component('Auth/403')
        )->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Staff_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->staff()->create());
        //Model trying to be accessed
        $role = Role::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $role);

        //expected Response
        $response->assertInertia(fn(AssertableInertia $inertia) => $inertia
            ->component('Auth/403')
        )->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Parent_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->parent()->create());
        //Model trying to be accessed
        $role = Role::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $role);

        //expected Response
        $response->assertInertia(fn(AssertableInertia $inertia) => $inertia
            ->component('Auth/403')
        )->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->global()->create());

        //Model trying to be accessed
        $role = Role::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $role);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(Role $role) => $this->get(action([\App\Http\Controllers\RoleController::class, 'index']))];
        yield [fn(Role $role) => $this->get(action([\App\Http\Controllers\RoleController::class, 'create']))];
        yield [fn(Role $role) => $this->post(action([\App\Http\Controllers\RoleController::class, 'store']))];
        yield [fn(Role $role) => $this->delete(action([\App\Http\Controllers\RoleController::class, 'delete'], $role->id))];

    }

}

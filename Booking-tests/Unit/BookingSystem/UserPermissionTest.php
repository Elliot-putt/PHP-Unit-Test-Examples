<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia;

class UserPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Guest_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->guest()->create());
        //Model trying to be accessed
        $user = \App\Models\User::factory()->global()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $user);

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
        $user = \App\Models\User::factory()->global()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $user);

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
        $user = \App\Models\User::factory()->global()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $user);

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
        $user = \App\Models\User::factory()->global()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $user);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(User $user) => $this->get(action([\App\Http\Controllers\UserController::class, 'index']))];
        yield [fn(User $user) => $this->get(action([\App\Http\Controllers\UserController::class, 'create']))];
        yield [fn(User $user) => $this->post(action([\App\Http\Controllers\UserController::class, 'store']))];
        yield [fn(User $user) => $this->post(action([\App\Http\Controllers\UserController::class, 'update'], $user->id))];
        yield [fn(User $user) => $this->delete(action([\App\Http\Controllers\UserController::class, 'delete'], $user->id))];

    }

}

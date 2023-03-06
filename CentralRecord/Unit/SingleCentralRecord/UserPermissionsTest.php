<?php

namespace Tests\Unit\SingleCentralRecord;

use App\Models\User;
use Closure;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use function action;

class UserPermissionsTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $user = User::factory()->global()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $user);

        //expected Response
        $response->assertForbidden();
    }
    /**
     * @test
     * @dataProvider requests
     */
    public function test_Managers_Users_Are_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->manager()->create());

        //Model trying to be accessed
        $user = User::factory()->temporary()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $user);

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


        //Model trying to be accessed
        $user = User::factory()->temporary()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $user);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(User $user) => $this->get(action([\App\Http\Controllers\UserController::class, 'index']))];
        yield [fn(User $user) => $this->get(action([\App\Http\Controllers\UserController::class, 'create']))];
        yield [fn(User $user) => $this->get(action([\App\Http\Controllers\UserController::class, 'show'], $user->id))];
        yield [fn(User $user) => $this->post(action([\App\Http\Controllers\UserController::class, 'store']))];
        yield [fn(User $user) => $this->delete(action([\App\Http\Controllers\UserController::class, 'destroy'], $user->id))];

    }

}

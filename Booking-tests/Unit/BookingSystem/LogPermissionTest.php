<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LogPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Guest_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->guest()->create());
        //Model trying to be accessed
        $log = \App\Models\Log::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $log);

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
        $log = \App\Models\Log::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $log);

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
        $log = \App\Models\Log::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $log);

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
        $log = \App\Models\Log::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $log);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Log $log) => $this->get(action([\App\Http\Controllers\LogController::class, 'index']))];
    }

}

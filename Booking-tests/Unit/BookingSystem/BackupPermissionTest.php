<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class BackupPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Guest_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->guest()->create());

        /** @var TestResponse $response */
        $response = $sendRequest->call($this);

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
        /** @var TestResponse $response */
        $response = $sendRequest->call($this);

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
        /** @var TestResponse $response */
        $response = $sendRequest->call($this);

        //expected Response
        $response->assertInertia(fn(AssertableInertia $inertia) => $inertia
            ->component('Auth/403')
        )->assertForbidden();
    }

//    /**
//     * @test
//     * @dataProvider requests
//     */
//    public function test_Global_Users_Are_Allowed_User_Access(Closure $sendRequest)
//    {
//        //User Trying to complete the action
//        $this->login(User::factory()->global()->create());
//
//        /** @var TestResponse $response */
//        $response = $sendRequest->call($this);
//
//        //expected Response
//        $this->assertTrue(in_array($response->status(), [200, 302]));
//    }

    public function requests(): \Generator
    {
        yield [fn() => $this->get(action([\App\Http\Controllers\BackupController::class, 'index']))];
        yield [fn() => $this->get(action([\App\Http\Controllers\BackupController::class, 'dbBackup']))];
        yield [fn() => $this->get(action([\App\Http\Controllers\BackupController::class, 'fullBackup']))];
        yield [fn() => $this->get(action([\App\Http\Controllers\BackupController::class, 'destroy']))];
    }

}

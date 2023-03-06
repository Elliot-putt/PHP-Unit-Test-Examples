<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CheckPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_Check_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $check = \App\Models\Check::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $check);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Manager_Users_Are_Allowed_Check_Access(Closure $sendRequest)
    {
        $manager = $this->login(User::factory()->manager()->create());

        $check = \App\Models\Check::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $check);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_Check_Access(Closure $sendRequest)
    {
        $global = $this->login();
        $check = \App\Models\Check::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $check);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Check $check) => $this->post(action([\App\Http\Controllers\CheckController::class, 'store']))];
        yield [fn(\App\Models\Check $check) => $this->post(action([\App\Http\Controllers\CheckController::class, 'delete'], $check->id))];


    }

}

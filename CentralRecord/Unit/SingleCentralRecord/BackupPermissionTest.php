<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BackupPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_Backup_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->temporary()->create());

        /** @var TestResponse $response */
        $response = $sendRequest->call($this);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Manager_Users_Are_Not_Allowed_Backup_Access(Closure $sendRequest)
    {
        $manager = $this->login(User::factory()->manager()->create());

        /** @var TestResponse $response */
        $response = $sendRequest->call($this);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_Backup_Access(Closure $sendRequest)
    {
        $global = $this->login();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn() => $this->get(action([\App\Http\Controllers\BackupController::class, 'index']))];
        yield [fn() => $this->get(action([\App\Http\Controllers\BackupController::class, 'destroy']))];
    }

}

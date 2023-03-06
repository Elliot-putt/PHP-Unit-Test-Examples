<?php

namespace Tests;

use App\Jobs\FileUpload;
use App\Jobs\RoleBoot;
use App\Jobs\SettingBoot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Bus;

abstract class TestCase extends BaseTestCase {

    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        Parent::setUp();

        Bus::fake([FileUpload::class]);
    }

    public function login(User $user = null): User
    {
        RoleBoot::dispatch();
        SettingBoot::dispatch();
        $user ??= $user = User::factory()->global()->create();
        $this->actingAs($user);

        return $user;
    }

}

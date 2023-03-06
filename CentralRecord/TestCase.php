<?php

namespace Tests;

use App\Http\Controllers\UserController;
use App\Jobs\FileUpload;
use App\Jobs\RoleBoot;
use App\Jobs\SettingBoot;
use App\Models\Location;
use App\Models\User;
use Bus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
    public function createLocationRelationships(User $user, int $amount = 3): User
    {
        $locations = Location::factory()->count($amount)->create();
        $arr = [];
        foreach($locations as $location)
        {
            $arr[] = $location->id;
        }
        $locationString = implode(',', $arr);

        $this->actingAs($user)->post(action([UserController::class, 'setPermission'], $user->id), [
          'permission_ids' => $locationString,
        ]);

        return $user;
    }

    public function assignLocationsRelationship(array $locationArray, User $user): User
    {
        $locations = Location::whereIn('id', $locationArray)->get();

        $arr = [];
        foreach($locations as $location)
        {
            $arr[] = $location->id;
        }
        $locationString = implode(',', $arr);

        $this->actingAs($user)->post(action([UserController::class, 'setPermission'], $user->id), [
            'permission_ids' => $locationString,
        ]);

        return $user->refresh();

    }

}

<?php

namespace Tests\Feature\SingleCentralRecord;

use App\Http\Controllers\OfficeLoginController;
use App\Http\Controllers\UserController;
use App\Jobs\FileUpload;
use App\Models\Location;
use App\Models\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class UserFileUploadTest extends TestCase {

    public function test_Uploading_A_User_Profile_Image_Dispatch()
    {

        $this->withoutExceptionHandling();
        $user = $this->login();
        \Bus::fake();

        $locations = Location::factory()->count(1)->create();
        $location = $locations->first();

        $file = UploadedFile::fake()->image('profile.jpg');

        $this->get(action([UserController::class, 'create']))
            ->assertStatus(200)
            ->assertViewIs('users.create');

        $this->assertDatabaseMissing(Media::class, [
            'name' => $file->getClientOriginalName(),
        ]);

        $this->post(action([UserController::class, 'store']), [
            'firstName' => 'James',
            'lastName' => 'Putt',
            'email' => 'James.putt@clpt.co.uk',
            'location_id' => $location->id,
            'role' => '1',
            'manager_id' => '1',
            //profileImage
            'file[]' => $file,
        ])->assertRedirect(route('users.index'));

        Bus::assertDispatched(FileUpload::class);

        $this->assertDatabaseHas(User::class, [
            'email' => 'James.putt@clpt.co.uk',
        ]);
    }

}

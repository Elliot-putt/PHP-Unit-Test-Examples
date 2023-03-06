<?php

namespace Tests\Feature\BookingSystem;

use App\Http\Controllers\UserController;
use App\Jobs\FileUpload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class UserFileUploadTest extends TestCase {

    public function test_Uploading_A_User_Profile_Image_Dispatch()
    {

        $this->withoutExceptionHandling();
        $user = $this->login();
        Bus::fake();

        $file = UploadedFile::fake()->image('profile.jpg');

        $this->get(action([UserController::class, 'create']))
            ->assertStatus(200)
            ->assertInertia();

        $this->assertDatabaseMissing(Media::class, [
            'name' => $file->getClientOriginalName(),
        ]);

        $this->post(action([UserController::class, 'store']), [
            'name' => 'James putt',
            'password' => 'password',
            'email' => 'James.putt@clpt.co.uk',
            //profileImage
            'file' => $file,
        ])->assertRedirect(route('users.index'));

        Bus::assertDispatched(FileUpload::class);

        $this->assertDatabaseHas(User::class, [
            'name' => strtolower('james putt'),
        ]);
    }

}

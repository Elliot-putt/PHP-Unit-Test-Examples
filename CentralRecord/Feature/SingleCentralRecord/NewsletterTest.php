<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterTest extends TestCase {

    public function test_Newsletter_dispatch()
    {
        $this->withoutExceptionHandling();

        \Illuminate\Support\Facades\Notification::fake();

        $user = $this->login(User::factory(['email' => 'elliot.putt@clpt.co.uk'])->global()->create());

        $setting = \App\Models\Setting::notification('general');

        //add notifiable settings to user
        $this->actingAs($user)->patch(action([\App\Http\Controllers\UserController::class, 'update'], $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'location_id' => $user->location_id,
            'role' => $user->role_id,
            'manager_id' => $user->manager_id,
            'staff_id' => $user->staff_id,
            $setting->name => 1,
        ])->assertRedirect(action([\App\Http\Controllers\UserController::class, 'show'], $user->id));

        //get new settings
        $general = $setting->fresh();
        $notifiableUser = $user->fresh();
        //check the user is notifiable
        $this->assertDatabaseHas(\App\Models\Setting::class, [
            'name' => $general->name,
            'value' => $general->value,
        ]);

        //posting notification
        $this->actingAs($notifiableUser)->get(action([\App\Http\Controllers\NotificationController::class, 'newsletter']))
            ->assertSuccessful()
            ->assertStatus(200);

        //check if notification was sent
        \Illuminate\Support\Facades\Notification::assertSentOnDemand(
            \App\Notifications\Newsletter::class
        );
    }

}

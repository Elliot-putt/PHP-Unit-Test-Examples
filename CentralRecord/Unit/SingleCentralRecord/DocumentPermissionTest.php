<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DocumentPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Temp_Users_Are_Not_Allowed_Document_Access(Closure $sendRequest)
    {
        //mock time
        $this->travelTo(now());
        //User Trying to complete the action
        $temp = $this->login(User::factory()->temporary()->create());

        //Model trying to be accessed
        $staff = \App\Models\Staff::factory()->create();
        $check = \App\Models\Check::factory()->create();
        $location = \App\Models\Location::factory()->create();
        $this->assignLocationsRelationship($location->pluck('id')->toArray(), $temp);
        $staffType = \App\Models\StaffType::factory()->create();
        $locationStaff = \App\Models\LocationStaff::create([
            'staff_id' => $staff->id,
            'staff_type_id' => $staffType->id,
            'location_id' => $location->id,
        ]);
        $document = \App\Models\Document::factory([
            'check_id' => $check->id,
            'actioned_by' => null,
            'staff_id' => $staff->id,
            'status' => null,
            'actioned_date' => null,
            'expiry_date' => \Carbon\Carbon::now()->addYears(3),
        ])->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $document);

        //expected Response
        $response->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Managers_Users_Are_Allowed_Document_Access(Closure $sendRequest)
    {
        //mock time
        $this->travelTo(now());
        //User Trying to complete the action
        $manager = $this->login(User::factory()->manager()->create());
        $staff = \App\Models\Staff::factory()->create();
        $check = \App\Models\Check::factory()->create();
        $location = \App\Models\Location::factory()->create();
        $this->assignLocationsRelationship($location->pluck('id')->toArray(), $manager);
        $staffType = \App\Models\StaffType::factory()->create();
        $locationStaff = \App\Models\LocationStaff::create([
            'staff_id' => $staff->id,
            'staff_type_id' => $staffType->id,
            'location_id' => $location->id,
        ]);
        $document = \App\Models\Document::factory([
            'check_id' => $check->id,
            'actioned_by' => null,
            'staff_id' => $staff->id,
            'status' => null,
            'actioned_date' => null,
            'expiry_date' => \Carbon\Carbon::now()->addYears(3),
        ])->create();
        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $document);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Global_Users_Are_Allowed_Document_Access(Closure $sendRequest)
    {

        //mock time
        $this->travelTo(now());
        //User Trying to complete the action
        $global = $this->login();
        $staff = \App\Models\Staff::factory()->create();
        $check = \App\Models\Check::factory()->create();
        $location = \App\Models\Location::factory()->create();
        $this->assignLocationsRelationship($location->pluck('id')->toArray(), $global);
        $staffType = \App\Models\StaffType::factory()->create();
        $locationStaff = \App\Models\LocationStaff::create([
            'staff_id' => $staff->id,
            'staff_type_id' => $staffType->id,
            'location_id' => $location->id,
        ]);
        $document = \App\Models\Document::factory([
            'check_id' => $check->id,
            'actioned_by' => null,
            'staff_id' => $staff->id,
            'status' => null,
            'actioned_date' => null,
            'expiry_date' => \Carbon\Carbon::now()->addYears(3),
        ])->create();
        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $document);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Document $document) => $this->get(action([\App\Http\Controllers\DocumentController::class, 'index']))];
        yield [fn(\App\Models\Document $document) => $this->post(action([\App\Http\Controllers\DocumentController::class, 'createFile'], $document->staff_id))];
        yield [fn(\App\Models\Document $document) => $this->post(action([\App\Http\Controllers\DocumentController::class, 'createCheck'], $document->staff_id))];
        yield [fn(\App\Models\Document $document) => $this->post(action([\App\Http\Controllers\DocumentController::class, 'renewCheck'], $document->id))];
        yield [fn(\App\Models\Document $document) => $this->get(action([\App\Http\Controllers\DocumentController::class, 'approve'], $document->id))];
        yield [fn(\App\Models\Document $document) => $this->get(action([\App\Http\Controllers\DocumentController::class, 'deny'], $document->id))];
        yield [fn(\App\Models\Document $document) => $this->delete(action([\App\Http\Controllers\DocumentController::class, 'delete'], $document->id))];

    }

}

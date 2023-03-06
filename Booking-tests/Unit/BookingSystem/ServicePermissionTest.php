<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ServicePermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Guest_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->guest()->create());
        //Model trying to be accessed
        $service = \App\Models\Service::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $service);

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
        //assigning the logged-in user to the company

        $service = \App\Models\Service::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $service);

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
        $service = \App\Models\Service::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $service);

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

        //assigning the logged-in user to the company
        $this->post(action([\App\Http\Controllers\CompanyController::class, 'store']), [
            'name' => 'heath park',
            'address_1' => 'Greenacres Avenue, Underhill Wolverhampton',
            'city' => 'Wolverhampton',
            'county' => 'West Midlands',
            'postcode' => 'WV10 8NZ',
            'email' => 'info@westcroftschool.co.uk',
            'url' => 'https://www.westcroftschool.co.uk/',
        ]);
        $company = \App\Models\Company::first();
        //Model trying to be accessed
        $service = \App\Models\Service::factory(['company_id' => $company->id])->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $service);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Service $service) => $this->get(action([\App\Http\Controllers\ServiceController::class, 'create'], $service->company->id))];
        yield [fn(\App\Models\Service $service) => $this->post(action([\App\Http\Controllers\ServiceController::class, 'store'], $service->company->id))];
        yield [fn(\App\Models\Service $service) => $this->post(action([\App\Http\Controllers\ServiceController::class, 'update'], $service->id))];
        yield [fn(\App\Models\Service $service) => $this->delete(action([\App\Http\Controllers\ServiceController::class, 'delete'], $service->id))];

    }

}

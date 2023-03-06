<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CompanyPermissionTest extends TestCase {

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Guest_Users_Are_Not_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->guest()->create());
        //Model trying to be accessed
        $company = \App\Models\Company::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $company);

        //expected Response
        $response->assertInertia(fn(AssertableInertia $inertia) => $inertia
            ->component('Auth/403')
        )->assertForbidden();
    }

    /**
     * @test
     * @dataProvider requests
     */
    public function test_Staff_Users_Are_Allowed_User_Access(Closure $sendRequest)
    {
        //User Trying to complete the action
        $this->login(User::factory()->staff()->create());
        //assigning the logged-in user to the company

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

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $company);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
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
        $company = \App\Models\Company::factory()->create();

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $company);

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
        // model trying to be access
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

        /** @var TestResponse $response */
        $response = $sendRequest->call($this, $company);

        //expected Response
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    public function requests(): \Generator
    {
        yield [fn(\App\Models\Company $company) => $this->get(action([\App\Http\Controllers\CompanyController::class, 'create']))];
        yield [fn(\App\Models\Company $company) => $this->get(action([\App\Http\Controllers\CompanyController::class, 'store']))];
        yield [fn(\App\Models\Company $company) => $this->post(action([\App\Http\Controllers\CompanyController::class, 'update'], $company->id))];
        yield [fn(\App\Models\Company $company) => $this->delete(action([\App\Http\Controllers\CompanyController::class, 'delete'], $company->id))];

    }

}

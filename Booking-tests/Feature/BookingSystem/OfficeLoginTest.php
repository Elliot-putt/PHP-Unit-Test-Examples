<?php

use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class OfficeLoginTest extends TestCase {

    public function test_Office_Login_With_Mocking_Laravel_Socialite()
    {
        //Mock Laravel Socialite User
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        //Create a Fake User
        $abstractUser
            ->allows('getEmail')
            ->andReturns('elliot.putt@clpt.co.uk')
            ->shouldReceive('getName')
            ->andReturn('Elliot Putt');
        //Mock Laravel Socialite provider or ('azure')
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

        //pass the user to the provider
        $provider
            ->allows('user')
            ->andReturns($abstractUser);
        //expect what you should receive
        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);

        $this->get(route("office.login"))->assertStatus(500);

    }

}

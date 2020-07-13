<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\User;
use Illuminate\Http\Response;
use Faker\Factory;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    use InteractsWithAuthentication;

    public function testUserRegister()
    {
        $faker = Factory::create();
        $data['first_name'] = $faker->firstName;
        $data['last_name'] = $faker->lastName;
        $data['username'] = $faker->userName;
        $data['email'] = $faker->email;
        $data['phone'] = $faker->phoneNumber;
        $data['password'] = $password = $faker->password;
        $data['password_confirmation'] = $password;

        //dd($data['phone']);
        $response = $this->post('api/register', $data);
        $response->assertStatus(200);
    }

    public function testUserLogin()
    {
        $faker = Factory::create();
        $password = 'test';
        $user = factory(User::class)->create(['password' => Hash::make($password)]);

        //with email
        $data['email_or_username'] = $user->email;
        $data['password'] = $password;
        $response = $this->post('api/login', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
        //for username
        $data['email_or_username'] = $user->username;
        $data['password'] = $password;
        $response = $this->post('api/login', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function testUserLogout()
    {
        $user = \factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->post('api/logout');
        $response->assertStatus(200);

        //Assert token was cleared
        $user->refresh();
        $this->assertNull($user->api_token);
    }
}

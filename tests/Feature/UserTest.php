<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\WithFaker;
use App\User;
use Tests\TestCase;
use Faker\Factory;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    use InteractsWithAuthentication;
    public function testGetUser()
    {
        $user = \factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get('api/getUser');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'first_name',
            'last_name',
            'username',
            'email',
            'phone',
            'password'
        ]);
    }

    public function testUpdateUser()
    {
        $faker = Factory::create();
        $data['first_name'] = $faker->firstName;
        $data['last_name'] = $faker->lastName;
        $data['username'] = $faker->userName;
        $data['email'] = $faker->email;
        $data['phone'] = $faker->phoneNumber;

        /** @var User $user */
        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $response = $this->patch("api/updateUser", $data);
        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone
        ], $data);
    }

    public function testUpdateUserPassword()
    {
        $faker = Factory::create();
        $data['password'] = $faker->username;

        $user = \factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->patch("api/updateUserPassword", $data);
        $response->assertStatus(200);
        $user->refresh();
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }
}

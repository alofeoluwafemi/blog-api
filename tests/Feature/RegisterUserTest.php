<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase,WithoutMiddleware;

    public function testCreateUser()
    {
        $faker = App::make('Faker\Generator');

        $response = $this->json('POST','api/v1/user/create',[
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => bcrypt('abcd1234')
        ]);

        $data = json_decode($response->getContent());

        $this->assertObjectHasAttribute('data',$data);
    }
}
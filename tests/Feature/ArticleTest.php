<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ArticleTest extends TestCase
{
    use RefreshDatabase,WithoutMiddleware;

    public function testCreateArticle()
    {
        $user = factory(User::class)->create();

        $faker = App::make('Faker\Generator');

        $response = $this->actingAs($user)->json('POST','api/v1/article/create',[
            'title' => $faker->sentence,
            'description' => $faker->paragraph(10),
        ]);

        $response->assertStatus(200);

        $response->assertSee('title');

        $this->assertObjectHasAttribute('data',json_decode($response->getContent()));
    }

    public function testListArticles()
    {
        $response   = $this->json('GET','api/v1/articles');
        $data       = json_decode($response->getContent());

        $this->assertObjectHasAttribute('data',$data);

        $response->assertStatus(200);
    }

    public function testViewArticle()
    {
        $this->json('GET',"api/v1/article/1")->assertSuccessful();
    }

    public function testUpdateArticle()
    {
        $user = factory(User::class)->create();

        $faker = App::make('Faker\Generator');

        $response = $this->actingAs($user)->json('PUT',"api/v1/article/1",[
            'title' => $faker->sentence,
            'description' => $faker->paragraph(10),
        ]);

        $this->assertObjectHasAttribute('data',json_decode($response->getContent()));
    }

    public function testCommentOnArticle()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->json('POST',"api/v1/article/1/comment",[
            'user_id' => $user->id,
            'content' => 'A random comment from a random user'
        ]);

        dd($response);

        $this->assertObjectHasAttribute('data',json_decode($response->getContent()));
    }

    public function testDeleteArticle()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->json('DELETE',"api/v1/article/1");

        $this->assertTrue(json_decode($response->getContent()));
    }
}
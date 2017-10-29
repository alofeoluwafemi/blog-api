<?php

namespace Tests\Unit;

use App\Article;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IfDatabaseContainsArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIfUserDataExistTest()
    {
        $user = factory(User::class)->create();

        $this->assertDatabaseHas('users', $user->toArray());
    }

    public function testIfArticleDataExistTest()
    {
        $article = factory(Article::class)->create(['user_id' => (factory(User::class)->create())->id]);

        $this->assertDatabaseHas('articles', $article->toArray());
    }
}

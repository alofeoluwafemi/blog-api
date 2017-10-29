<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'],function($route)
{
   $route->post('article/create',['uses' => 'ArticleController@createNewArticle'])->middleware('auth:api');
   $route->get('articles',['uses' => 'ArticleController@getAllArticles']);
   $route->get('article/{id}',['uses' => 'ArticleController@viewArticle'])->where(['id' => '[0-9]']);
   $route->put('article/{id}',['uses' => 'ArticleController@updateArticle'])->where(['id' => '[0-9]']);
   $route->delete('article/{id}',['uses' => 'ArticleController@deleteArticle'])->where(['id' => '[0-9]']);
   $route->post('article/{id}/comment',['uses' => 'ArticleController@addArticleComment'])->where(['id' => '[0-9]']);
});
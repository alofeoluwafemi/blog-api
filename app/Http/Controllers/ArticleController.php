<?php

namespace App\Http\Controllers;

use App\Comment as CommentModel;
use App\Http\Requests\Comment;
use App\Http\Requests\Article;
use App\Article as ArticleModel;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

class ArticleController extends Controller
{
    private $fractal;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    /**
     * Fetch all article lists
     * 10 per page
     * @param ArticleModel $article
     * @param ?page
     * @return array
     */
    public function getAllArticles(ArticleModel $article)
    {
        $articles = $article->paginate(5);

        $fractal = new Manager();

        $resource = new Collection($articles, function($article) {
            return [
                'id'            => (int) $article['id'],
                'title'         => $article['title'],
                'description'   => $article['description'],
                'published'     => $article->created_at,
                'comments'      => $article->comments->each(function($comment)
                {
                    return [
                        'comment'     => $comment->content,
                        'published'   => $comment->created_at,
                        'user'        => $comment->user->name
                    ];
                }),
                'links'   => [
                    [
                        'rel' => 'self',
                        'uri' => 'api/v1/article/'.$article['id'],
                    ]
                ]
            ];
        });

        $resource->setPaginator(new IlluminatePaginatorAdapter($articles));

        $fractal->setSerializer(new DataArraySerializer);

        return $fractal->createData($resource)->toArray();
    }

    /**
     * Create a new article
     * @param Article $request
     * @return string
     */
    public function createNewArticle(Article $request)
    {
        $article = auth()->user()->articles()->save(new ArticleModel($request->all()));

        return $this->singleItem($article);
    }

    /**
     * View article details
     * @param $id
     * @return string
     */
    public function viewArticle($id)
    {
        $article = ArticleModel::findOrFail($id);

        return $this->singleItem($article);
    }

    /**
     * Update an article
     * @param Article $request
     * @param $id
     * @return string
     */
    public function updateArticle(Article $request, $id)
    {
        $article = ArticleModel::findOrFail($id);

        $article->update($request->all());

        return $this->singleItem($article);
    }

    public function deleteArticle($id)
    {
        $article = ArticleModel::findOrFail($id);

        return response()->json($article->delete());
    }

    public function addArticleComment(Comment $request,$id)
    {
        $article = ArticleModel::findOrFail($id);

        $article->comments()->save(new CommentModel($request->all()));

        return $this->singleItem($article);
    }

    private function singleItem($data)
    {
        $resource = new Item($data, function($article) {
            return [
                'id'            => (int) $article->id,
                'title'         => $article->title,
                'description'   => $article->description,
                'published'     => $article->created_at,
                'comments'      => $article->comments->each(function($comment)
                {
                    return [
                        'comment'     => $comment->content,
                        'published'   => $comment->created_at,
                        'user'        => $comment->user->name
                    ];
                }),
                'links'   => [
                    [
                        'rel' => 'self',
                        'uri' => 'api/v1/article/'.$article['id'],
                    ]
                ]
            ];
        });

        return $this->fractal->createData($resource)->toJson();
    }
}

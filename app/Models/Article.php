<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Article extends Model
{

    public $guarded = [];
    public $timestamps = true;


    public function author()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'tagable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'attachable');
    }

    public static function saveArticle(Request $request)
    {
        $articleModel = self::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $request->user()->id
            ]
        );

        if (!empty($request->tags)) {
            $articleModel->tags()->delete();
            $articleModel->tags()->createMany($request->tags);
        }

        if (!empty($request->allFiles())) {
            File::attachFiles($articleModel, array_values($request->allFiles()));
        }
    }

    public function scopeGetList($query, Request $request)
    {
        $order = $request->order ?? 'desc';
        $type = $request->type;

        if ($type === 'newest') {
            $query = $query->orderBy('created_at', $order);
        } else if ($type === 'rating') {
            $query = $query->orderBy('rating', $order);
        } else if ($type === 'comments') {
            $query = $query->orderBy('comments_count', $order);
        }

        return $query;
    }

    public static function getBlogersRatingList(Request $request)
    {
        return self::selectRaw('
        ANY_VALUE(users.id) as id,
        ANY_VALUE(users.name) as name,
        ANY_VALUE(users.avatar) as avatar,
        SUM(IF(ratings.rating, ratings.rating + articles.views, articles.views)) as bloger_rating     
        ')->join('users', 'users.id', '=', 'articles.user_id')
            ->leftJoin('ratings', 'ratings.article_id', '=', 'articles.id')
            ->groupBy('users.id')
            ->orderBy('bloger_rating', 'desc');
    }

    public static function getMostPopularArticles(Request $request)
    {
        return self::selectRaw('
         articles.title,
         articles.body,
         ANY_VALUE(users.id) as author_id,
         ANY_VALUE(users.name) as author_name,
         ANY_VALUE(users.avatar) as author_avatar,
         SUM(IF(ratings.rating, ratings.rating + articles.views, articles.views)) as bloger_rating
         ')->leftJoin('ratings', 'ratings.article_id', '=', 'articles.id')
            ->leftJoin('users', 'users.id', '=', 'articles.user_id')
            ->whereRaw('UNIX_TIMESTAMP(articles.created_at) BETWEEN ? AND ?', [Carbon::now()->subMonth()->unix(), Carbon::now()->unix()])
            ->groupBy('articles.id')
            ->orderBy('bloger_rating', 'desc');
    }


    protected static function boot()
    {
        parent::boot();

        // cause a delete of a poster to cascade
        // to children so they are also deleted
        static::deleting(function ($article) {

            $article->tags()->delete();
            $article->comments()->delete();
        });

    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Article extends Model
{

    public $guarded = [];
    public $timestamps = true;


    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'tagable');
    }

    public function articleFiles()
    {
        return $this->morphMany(File::class, 'attachable');
    }

    public function commentsFiles()
    {
        return $this->hasManyThrough(File::class, Comment::class, null, 'attachable_id')
            ->where('attachable_type', Article::class);
    }

    public static function getOne(Request $request)
    {
        return self::with(['comments', 'author', 'tags', 'articleFiles'])->withCount(['comments'])->where('id', $request->id)->get();
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
        $beginDate = $request->beginDate;
        $endDate = $request->endDate;

        if ($type === 'newest') {
            $query = $query->orderBy('created_at', $order);
        } else if ($type === 'rating') {
            $query = $query->orderBy('rating', $order);
        } else if ($type === 'comments') {
            $query = $query->orderBy('comments_count', $order);
        }


        if ($beginDate && $endDate) {
            $query = $query->whereRaw('UNIX_TIMESTAMP(created_at) BETWEEN  ? AND ?', [$beginDate / 1000, $endDate / 1000]);
        }

        return $query;
    }

    public static function scopeGetBlogersRatingList($query)
    {
        return $query->selectRaw('
        ANY_VALUE(users.id) as id,
        ANY_VALUE(users.name) as name,
        ANY_VALUE(users.avatar) as avatar,
        SUM(IF(ratings.rating, ratings.rating + articles.views, articles.views)) as bloger_rating     
        ')->join('users', 'users.id', '=', 'articles.user_id')
            ->leftJoin('ratings', 'ratings.article_id', '=', 'articles.id')
            ->groupBy('users.id')
            ->orderBy('bloger_rating', 'desc');
    }

    public function scopeGetMostPopularArticles($query)
    {
        return $query->selectRaw('
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
        static::deleting(function (Article $article) {

            $article->tags()->delete();
            foreach ($article->commentsFiles() as $file) {
                $file->delete();
            }

            foreach ($article->articleFiles() as $file) {
                $file->delete();
            }
        });

    }

}

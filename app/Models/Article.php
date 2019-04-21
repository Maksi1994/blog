<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

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

    public static function save(Request $request)
    {
      $articleModel = self::updateOrCreate(
          [
            'id' => $request->id
          ],
          [
              'title' => $request->title,
              'body' => $request->body,
              'user_id' => 1
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

<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Comment extends Model
{

    public $timestamps = true;
    protected $guarded = [];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'attachable');
    }

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public static function saveComment(Request $request)
    {
        $model = null;
        $commentModel = null;

        switch ($request->type) {
            case 'article':
                $model = Article::find($request->id);
                break;
            case 'reply':
                $model = Comment::find($request->id);
        }

        if ($model) {
            $commentModel = $model->comments()->updateOrCreate(
                [
                    'id' => $request->comment_id
                ],
                [
                    'body' => $request->body,
                    'parent_id' => $request->parent_id,
                    'user_id' => $request->user()->id
                ]
            );

            if (!empty($request->allFiles())) {
                File::attachFiles($commentModel, array_values($request->allFiles()));
            }
        }
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($comment) {
            foreach ($comment->files as $file) {
                $file->delete();
            }
        });
    }
}

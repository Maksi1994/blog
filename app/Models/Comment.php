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

    public static function saveComment(Request $request)
    {
        $model = null;
        $commentModel = null;
        $files = [];

        switch ($request->type) {
            case 'article':
                $model = Article::find($request->id);
        }

        if ($model) {
            $commentModel = $model->comments()->updateOrCreate(
                ['id' => $request->comment_id],
                ['body' => $request->body]);

            foreach ($request->all() as $requestItem) {
                if ($requestItem instanceof UploadedFile) {
                    $files[] = $requestItem;
                }
            }

            if (!empty($files)) {
                File::attachFiles($commentModel, $files);
            }
        }
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($comment) {
            foreach ($comment->files as $file) {
                $file->delete();
            }
        });
    }
}

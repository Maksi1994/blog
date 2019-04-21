<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{

    public $timestamps = true;
    public $guarded = [];
    protected $appends = ['url'];

    public function attachable()
    {
        return $this->morphTo();
    }

    public static function attachFiles($model, $images)
    {
        $fileModels = [];

        foreach ($images as $index => $image) {
            $name = Storage::disk('space')->putFile('files', $image, 'public');
            $type = $image->getClientMimeType();
            $fileModels[] = compact('name', 'type');
        }

        foreach ($model->files as $file) {
            $file->delete();
        }

        $model->files()->createMany($fileModels);
    }

    public function getUrlAttribute() {
        return env('DO_SPACES_DOMAIN') . $this->name;
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($file) {
            Storage::disk('space')->delete($file->name);
        });
    }


}

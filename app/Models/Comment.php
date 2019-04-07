<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    public function commentable() {
        return $this->morphTo();
    }


    public function author() {
        return $this->belongsTo(User::class);
    }
}

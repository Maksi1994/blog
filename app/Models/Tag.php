<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = true;
    protected $guarded = [];

    public function tagable() {
        return $this->morphTo();
    }
}

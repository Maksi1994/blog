<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ResetPassword extends Model
{

    use Notifiable;

    public $timestamps = true;
    public $guarded = [];

    public static function start(Request $request)
    {
        $resetPassword = self::updateOrCreate([
            'email' => $request->email,
            'token' => Str::random(60)
        ]);

        $resetPassword->notify(new ResetPassword($resetPassword));
    }


}

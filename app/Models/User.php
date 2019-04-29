<?php

namespace App\Models;

use App\Notifications\UserRegistration;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public static function registUser(Request $request)
    {
        $avatar = null;

        if ($request->hasFile('avatar')) {
            $avatar = Storage::disk('space')->putFile('avatars', $request->avatar, 'public');
        }

        $user = new User();
        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->token = Str::random();
        $user->password = bcrypt($request->password);
        $user->avatar = $avatar;
        $user->email = $request->email;
        $user->save();

        $user->notify(new UserRegistration($user));
    }

    public static function updateUser(Request $request)
    {
        $avatar = null;
        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('space')->delete($user->avatar);
            }

            $avatar = Storage::disk('space')->putFile('avatars', $request->avatar, 'public');
        }

        self::where('id', $request->user()->id)->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'avatar' => $avatar
        ]);
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($user) {
            if ($user->avatar) {
                Storage::disk('space')->delete($user->avatar);
            }
        });
    }
}

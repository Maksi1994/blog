<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use App\Models\Article;

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

    public function articles() {
      return $this->hasMany(Article::class);
    }

    public static function registUser(Request $request) {
      if ($request->hasFile('avatar')) {
          $avatar = Storage::disk('space')->putFile('avatars', $request->avatar, 'public');
      }

      $user = self::create([
        'id' => $request->id
      ],[
        'name' => $request->first_name. ' '. $request->last_name,
        'token' => Str::random(),
        'password' => bcrypt($request->password),
        'avatar' => $avatar
      ]);

      $user->notify(new UserRegistration($user));
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($user) {
          if ($user->avatar) {
            Storage::disk('space')->delete($user->avatar);
          }
        });
    }
}

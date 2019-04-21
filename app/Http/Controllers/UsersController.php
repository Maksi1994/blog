<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function create(Request $requst) {
        $validation = Validator::make($requst->all(), [
            'id' => 'exists:users',
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'password' => 'required|min:6'
        ]);
        $success = false;

        if (!$validation->fails()) {
          User::registUser($requst);
          $success = true;
        }

        return $this->success($success);
    }

    public function update() {
      
    }

    public function acceptRegistration(Request $requst) {
      $user = User::where('token', $requst->token)->first();
      $success = false;

      if ($user) {
        $user->active = 1;
        $user->token = null;
        $user->save();
        $success = true;
      }

      return response()->redirect('/');
    }

    public function login(Request $requst) {
        $success = Auth::attempt($request->all(), $requst->remember_me);

        return $this->success($success);
    }

    public function logout() {
      $success = Auth::logout();

      return $this->success($success);
    }

    public function getCurrUser(Request $requst) {
      return new UserResource($requst->user());
    }
}

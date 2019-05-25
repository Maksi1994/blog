<?php

namespace App\Http\Controllers;

use App\Http\Resources\Favorite\FavoritesCollection;
use App\Http\Resources\User\UserResource;
use App\Models\Favorite;
use App\Models\ResetPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function regist(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|unique:users',
            'password' => 'required|min:6'
        ]);
        $success = false;

        if (!$validation->fails()) {
            User::registUser($request);
            $success = true;
        }

        return $this->success($success);
    }

    public function update(Request $request)
    {
        User::updateUser($request);

        return $this->success(true);
    }

    public function isAnotherPassword(Request $request)
    {
        $password = User::where('email', $request->email)->get()->first()->makeVisible('pasword')->password;

        return $this->success(!password_verify($request->password, $password));
    }

    public function acceptRegistration(Request $request)
    {
        $user = User::where('token', $request->token)->first();
        $success = false;

        if ($user) {
            $user->active = 1;
            $user->token = null;
            $user->save();
            $success = true;
        }

        return redirect('/');
    }

    public function beginResetingPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|exists:users'
        ]);
        $success = false;

        if ($validation->fails()) {
            ResetPassword::start($request);
            $success = true;
        }

        return $this->success($success);
    }

    public function isActualResetPassword(Request $request)
    {
        $resetPassword = (boolean)ResetPassword::where('token', $request->token)->get()->first();

        return $this->success($resetPassword);
    }

    public function resetPassword(Request $request)
    {
        $success = false;

        return $this->success($success);
    }




    public function login(Request $request)
    {
        $success = false;

        if (Auth::attempt($request->all(), $request->remember_me)) {
            $user = Auth::user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token->save();

            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addMonth(1);
            }

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        }

        return $this->success($success);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->success(true);
    }

    public function getCurrUser(Request $request)
    {
        return new UserResource($request->user());
    }
}

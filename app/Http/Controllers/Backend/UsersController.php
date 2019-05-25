<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\User\UsersCollection;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    public function getList(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'order_type' => 'required|in:created_at,articles_count,comments_count',
            'order' => 'in:desc,asc'
        ]);
        $users = [];

        if (!$validation->fails()) {
            $users = User::withCount(['articles', 'comments'])
                ->with('role')
                ->orderBy($request->order_type, $request->order ?? 'desc')
                ->paginate(20, '*', '*', $request->page ?? 1)
                ->get();
        }

        return new UsersCollection($users);
    }

    public function getUser(Request $request)
    {
        $user = User::withCount(['articles', 'comments'])
            ->with('role')
            ->where('id', $request->id)
            ->get();

        $theeMostPopular = Article::getMostPopularArticles()
            ->where('articles.user_id', $request->id)
            ->limit(3)
            ->get();

        return response()->json(compact('user', 'theeMostPopular'));
    }

    public function deleteUser(Request $request)
    {
        $success = (boolean)User::destroy($request->id);

        return $this->success($success);
    }

}

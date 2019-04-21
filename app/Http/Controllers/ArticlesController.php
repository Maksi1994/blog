<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'body' => 'required|min:30',
            'tags.*.name' => 'required'
        ]);
        $success = false;

        if (!$validator->fails()) {
            $success = true;
            Article::save($requst);
        }

        return $this->success($success);
    }

    public function getUserArticles(Request $request)
    {
        $articles = Article::with(['author', 'tags'])
            ->getList($request)
            ->paginate(15, '*', '*', $request->id);

        return $this->success($success);
    }

    public function delete(Request $request)
    {
        $success = (boolean)Article::destroy($request->id);

        return $this->success($success);
    }

}

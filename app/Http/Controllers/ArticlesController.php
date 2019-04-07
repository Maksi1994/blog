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
            'user_id' => 'required|exists:users,id',
            'tags.*.name' => 'required'
        ]);
        $success = false;
        $articleModel = null;

        if (!$validator->fails()) {
            $success = true;
            $val = $request->all();

            $articleModel = Article::updateOrCreate(
                ['id' => $request->id],
                [
                    'title' => $request->title,
                    'body' => $request->body,
                    'user_id' => $request->user_id
                ]
            );


            if (!empty($request->tags)) {
                $articleModel->tags()->delete();
                $articleModel->tags()->createMany($request->tags);
            }
            /*
            $articleModel->images()->createMany();

            if (count($request->images)) {

            }
            */

        }

        return response()->json(compact('success'));
    }

    public function getUserArticles(Request $request)
    {
        $articles = Article::with(['author', 'tags'])
            ->getList($request)
            ->paginate(15, '*', '*', $request->id);

        return response()->json($articles);
    }

    public function delete(Request $request)
    {
        $success = (boolean)Article::destroy($request->id);

        return response()->json(compact('success'));
    }
}

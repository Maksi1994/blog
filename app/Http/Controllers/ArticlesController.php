<?php

namespace App\Http\Controllers;

use App\Http\Resources\Articles\ArticleResource;
use App\Http\Resources\Main\ArticlesCollection;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{

    public function save(Request $request)
    {
        $articleModel = null;
        $validator = Validator::make($request->all(), [
            'id' => 'exists:articles',
            'title' => 'required|min:5',
            'body' => 'required|min:30',
            'tags.*.name' => 'required'
        ]);
        $success = false;

        if (!$validator->fails()) {
            if ($request->id) {
                $articleModel = Article::find($request->id);
                if (Gate::forUser($request->user())->allows('edit-article', $articleModel)) {
                    Article::saveArticle($request);
                    $success = true;
                }
            } else {
                Article::saveArticle($request);
                $success = true;
            }
        }

        return $this->success($success);
    }

    public function getList(Request $request)
    {
        $articles = Article::with(['author', 'tags'])->withCount('comments')
            ->getList($request)
            ->paginate(15, '*', '*', $request->id);

        return new ArticlesCollection($articles);
    }

    public function getOne(Request $request)
    {
        $article = Article::getOne($request);

        return new ArticleResource($article);
    }

    public function remove(Request $request)
    {
        $articleModel = Article::find($request->id);
        $success = false;

        if ($articleModel && Gate::forUser($request->user())->allows('delete-article', $articleModel)) {
            $articleModel->delete();
            $success = true;
        }

        return $this->success($success);
    }

}

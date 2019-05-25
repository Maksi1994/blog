<?php

namespace App\Http\Controllers;

use App\Http\Resources\Main\ArticlesCollection;
use App\Http\Resources\Main\BlogersCollection;
use App\Models\Article;
use Illuminate\Http\Request;

class MainController extends Controller
{

    public function getBlogersRatingList(Request $request)
    {
        $blogers = Article::getBlogersRatingList($request)->paginate(15, '*', '*', $request->page ?? 1);

        return new BlogersCollection($blogers);
    }

    public function getMostPopularArticles(Request $request)
    {
        $articles = Article::getMostPopularArticles($request)->paginate(10, '*', '*', $request->page ?? 1);

        return new ArticlesCollection($articles);
    }

    public function getLastArticles(Request $request)
    {
        $articles = Article::with(['user', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(20, '*', '*', $request->page ?? 1);

        return new ArticlesCollection($articles);
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\Main\ArticlesCollection;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticlesController extends Controller
{
    public function getList(Request $request) {
     $articles = Article::with(['user'])
         ->withCount(['commments'])
         ->orderBy('created_at', 'desc')
         ->paginate(20, '*', '*', $request->page);

     return new ArticlesCollection($articles);
    }

    public function getOne(Request $request)
    {

    }

}

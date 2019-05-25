<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\Comment\CommentCollection;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{

    public function getCurrOverview() {

    }

    public function getLastComments(Request $request)
    {
        $comments = Comment::paginate(20, '*', '*', $request->page ?? 1);

        return new CommentCollection($comments);
    }

    public function getLastArticles()
    {

    }


}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{

    public function create(Request $request) {
        $comment = Comment::create();


    }

    public function delete(Request $request) {

    }

}

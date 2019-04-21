<?php
namespace App\Http\Controllers;

use App\Http\Resources\Comment\CommentCollection;
use App\Models\Article;
use App\Models\Comment;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required',
            'body' => 'required|min:3',
            'type' => 'required|in:article,comment',
            'comment_id' => 'exists:comments,id'
        ]);
        $success = false;

        if (!$validation->fails()) {
             Comment::save($request);
             $success = true;
        }

        return $this->success($success);
    }

    public function getList(Request $request)
    {
        $comments = [];

        switch ($request->type) {
            case 'article':
                $comments = Article::find($request->id)->comments();
                break;
            case 'reply':
                $comments = Comment::find($request->id)->children();
        }

        if ($comments) {
            $comments = $comments->with('files')->paginate(20, '*', '*', $request->page ?? 1);
        }

        return new CommentCollection($comments);
    }

    public function remove(Request $request)
    {
        $success = (boolean)Comment::destroy($request->id);

        return $this->success($success);
    }

}

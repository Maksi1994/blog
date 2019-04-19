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
            'type' => 'in:article,comment',
            'comment_id' => 'exists:comments,id'
        ]);
        $success = false;

        if (!$validation->fails()) {
            Comment::saveComment($request);
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
                $comments = Comment::find($request->id)->comments();
        }

        if ($comments) {
            $comments = $comments->with('files')->paginate(20, '*', '*', $request->page ?? 1);
        }

        return (new CommentCollection($comments))->response();
    }

    public function downloadFile(Request $request)
    {
        $fileName = File::find($request->id)->url;
        $headers = [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'. $fileName .'"',
        ];

        return response()->make(Storage::disk('s3')->get($fileName), 200, $headers);
    }

    public function delete(Request $request)
    {
        $success = (boolean)Comment::destroy($request->id);

        return $this->success($success);
    }

}

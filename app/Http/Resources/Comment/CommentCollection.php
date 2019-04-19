<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\File\FileResource;
use App\Traits\CollectionPaginationTrait;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
{

    use CollectionPaginationTrait;

    public function __construct($resource)
    {
        $this->makePagination($resource);
        parent::__construct($resource->getCollection());
    }


    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'result' => $this->collection->map(function ($comment) {
                return [
                    'body' => $comment->body,
                    'files' => $comment->files->map(function ($file) {
                        return new FileResource($file);
                    })
                ];
            }),
            'meta' => $this->pagination
        ];
    }
}

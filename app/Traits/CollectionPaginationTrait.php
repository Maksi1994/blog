<?php

namespace App\Traits;


trait CollectionPaginationTrait {

    public $pagination;

    public function makePagination($resource) {
        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'per_page' => $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'total_pages' => $resource->lastPage()
        ];
    }
}

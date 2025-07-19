<?php

namespace Modules\User\Http\Resources\Api\V1\Admin;

 
use Illuminate\Http\Resources\Json\ResourceCollection;

class AdminCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => AdminResource::collection($this->collection),
        ];
    }

    public function with($request): array
    {
        return [
            'meta' => [
                'current_page' => $this->currentPage(),
                'per_page'     => $this->perPage(),
                'total'        => $this->total(),
                'last_page'    => $this->lastPage(),
            ],
        ];
    }
}

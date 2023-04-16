<?php

namespace App\Actions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->all();
    }
}

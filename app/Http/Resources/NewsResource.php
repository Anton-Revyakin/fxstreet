<?php

namespace App\Http\Resources;

use App\News;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NewsResource
 *
 * @package App\Http\Resources
 * @mixin News
 */
class NewsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title'    => $this->title,
            'body'     => $this->body,
            'full_url' => $this->full_url,
            'tags'     => json_decode($this->tags, true),
        ];
    }
}

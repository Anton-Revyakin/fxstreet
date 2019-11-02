<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * App\News
 *
 * @property int                             $id
 * @property string                          $title
 * @property string                          $body
 * @property string                          $original_link
 * @property string                          $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereOriginalLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string                          $business_id
 * @property string                          $full_url
 * @property mixed|null                      $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereFullUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereTags($value)
 * @property string                          $image
 * @method static \Illuminate\Database\Eloquent\Builder|\App\News whereImage($value)
 */
class News extends Model
{
    use Searchable;

    public function searchableAs()
    {
        return 'news_index';
    }

    public function toSearchableArray()
    {
        return [
            'id'           => $this->id,
            'image'        => $this->image,
            'title'        => $this->title,
            'published_at' => $this->published_at,
        ];
    }
}



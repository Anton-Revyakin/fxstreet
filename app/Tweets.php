<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Tweets
 *
 * @property int $id
 * @property string $business_id
 * @property string $image
 * @property string $body
 * @property string $published_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tweets whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tweets extends Model
{
    //
}

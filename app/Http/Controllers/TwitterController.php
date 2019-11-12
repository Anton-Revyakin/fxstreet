<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetNewsByDate;
use App\Tweets;
use Illuminate\Support\Collection;

class TwitterController extends Controller
{
    /**
     * Получить список твитов
     *
     * @param GetNewsByDate $request
     * @param Tweets        $tweets
     *
     * @return Collection
     */
    public function getList(GetNewsByDate $request, Tweets $tweets)
    {
        $validate = $request->validated();

        return $tweets
            ->select(['id', 'business_id', 'full_url', 'image', 'body', 'published_at'])
            ->whereDate('published_at', $request->date)
            ->orderByDesc('id')
            ->get();
    }
}

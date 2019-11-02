<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetNewsByDate;
use App\Http\Resources\NewsResource;
use App\News;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * Получить список новостей
     *
     * @param News          $news
     * @param GetNewsByDate $request
     *
     * @return string
     */
    public function getList(GetNewsByDate $request, News $news)
    {
        $validated = $request->validated();

        return $news
            ->select(['id', 'image', 'title', 'published_at'])
            ->whereDate('published_at', $request->date)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Получить новость по id
     *
     * @param News $news
     *
     * @return News
     */
    public function getById(News $news)
    {
        return new NewsResource($news);
    }

    public function search()
    {

    }
}

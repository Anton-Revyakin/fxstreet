<?php

namespace App\Console\Commands;

use App\News;
use Carbon\Carbon;
use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class FxStreetNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anton:fxstreet-get-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FxStreet. Get news';

    /**
     * Список новостей
     *
     * @var Collection
     */
    private $newsList;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->newsList = collect();
    }

    /**
     * Execute the console command.
     *
     * @param News $news
     *
     * @return mixed
     */
    public function handle(News $news): bool
    {
        //Получить заголовки новостей
        $this->getNewsTitles();

        //Удалить записи с существующими BusinessId
        echo 'Deleted: ' . $this->excludeDuplicates($news) . PHP_EOL;

        if ($this->newsList->isEmpty()) return false;

        $this->addNews($news);

        $news->whereIn('business_id', $this->newsList->pluck('BusinessId'))->searchable();

        return true;
    }

//    private function addNewsBodies(News $news)
//    {
//        //Получить новости без тела
//        $fullUrls = $news->whereNull('body')->orderByDesc('id')->get();
//
//        \DB::beginTransaction();
//
//        try {
//            $fullUrls->each(function ($value) {
//                $value->body = $this->getNewsBody($value->full_url);
//            });
//
//            $fullUrls->each->save();
//            \DB::commit();
//        } catch (\Exception $e) {
//            \DB::rollback();
//        }
//    }

    /**
     * Добавить новости
     *
     * @param News $news
     */
    private function addNews(News $news): void
    {
        $newsInsert = $this->newsList->map(function ($item) {
            return [
                'business_id'  => $item['BusinessId'],
                'image'        => $item['ImageUrl'],
                'title'        => $item['Title'],
                'body'         => $this->getNewsBody($item['FullUrl']),
                'full_url'     => $item['FullUrl'],
                'tags'         => json_encode($this->getTags($item['Tags'])),
                'published_at' => Carbon::createFromTimestamp($item['PublicationTime'])->format('Y-m-d H:i:s'),
            ];
        });

        $news->insert($newsInsert->reverse()->toArray());
    }

    /**
     * Выбрать валюты в тэгах
     *
     * @param array $tags
     *
     * @return array
     */
    private function getTags(array $tags): array
    {
        $tags = array_map(function ($item) {
            return preg_match('/^[A-Z]{6}$/', $item) ? $item : null;
        }, $tags);

        $tags = array_filter($tags, function ($item) {
            return $item;
        });

        return array_values($tags);
    }

    /**
     * Удалить записи с существующими BusinessId
     *
     * @param News $news
     *
     * @return int
     */
    private function excludeDuplicates(News $news): int
    {
        //Текущий размер коллекции
        $count = $this->newsList->count();

        //Получить записи в БД, по BusinessId (если есть)
        $businessIds = $news->whereIn('business_id', $this->newsList->pluck('BusinessId'))->get('business_id')->pluck('business_id');

        //Удалить записи с повторяющимися BusinessId
        if ($businessIds->isNotEmpty()) {
            $this->newsList = $this->newsList->filter(function ($value) use ($businessIds) {
                return !$businessIds->contains($value['BusinessId']);
            });
        }

        //Вернуть кол-во удаленных записей
        return $count - $this->newsList->count();
    }

    /**
     * Получить заголовки новостей
     */
    private function getNewsTitles(): void
    {
        try {
            $response = (new Client())->post(
                'https://50dev6p9k0-dsn.algolia.net/1/indexes/*/queries?x-algolia-agent=Algolia%20for%20vanilla%20JavaScript%20(lite)%203.25.1%3Binstantsearch.js%202.6.3%3BJS%20Helper%202.24.0&x-algolia-application-id=50DEV6P9K0&x-algolia-api-key=cd2dd138c8d64f40f6d06a60508312b0',
                [
                    'body' => json_encode([
                        'requests' => [
                            [
                                'indexName' => 'FxsIndexPro',
                                'params'    => http_build_query([
                                    'query'             => '',
                                    'hitsPerPage'       => 10,
                                    'maxValuesPerFacet' => 9999,
                                    'page'              => 0,
                                    'filters'           => 'CultureName:ru AND (Category:"Новости")',
                                    'facets'            => '["Tags","AuthorName"]',
                                    'tagFilters'        => '',
                                ]),
                            ],
                        ],
                    ]),
                ]
            );

            $response = $response->getBody()->getContents();
            $response = json_decode($response, true);
            $this->newsList = collect($response['results'][0]['hits']);
        } catch (RequestException $e) {
//            \Log::channel('sentrylog')->error($e->getMessage());
        }
    }

    /**
     * Получить тело новости
     *
     * @param string $url
     *
     * @return string|null
     */
    private function getNewsBody(string $url): ?string
    {
        $newsBodyDocument = new Document($url, true);
        if ($newsBodyDocument->count('#fxs_article_content') > 0) {
            $html = $newsBodyDocument->first('#fxs_article_content')->html();

            //Вырезать рекламу
            return preg_replace('|<iframe.+</iframe>|', '', $html);
        }

        return null;
    }
}

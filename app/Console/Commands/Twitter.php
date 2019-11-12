<?php

namespace App\Console\Commands;

use App\Tweets;
use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Twitter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anton:twitter-get-tweets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Twitter. Get tweets';

    /**
     * Список id твитов
     *
     * @var Collection
     */
    private $tweets;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->tweets = collect();
    }

    /**
     * Execute the console command.
     *
     * @param Tweets $tweets
     */
    public function handle(Tweets $tweets): void
    {
        $this->getTweets();

        dump($this->tweets);

        //Удалить записи с существующими BusinessId
        echo 'Deleted: ' . $this->excludeDuplicates($tweets) . PHP_EOL;

        if ($this->tweets->isEmpty()) exit(0);

        $this->addTweets($tweets);
    }

    /**
     * Получить твиты
     */
    private function getTweets(): void
    {
        try {
            $tweets = (new Client())->request(
                'GET',
                'https://twitter.com/i/profiles/show/realDonaldTrump/timeline/tweets?composed_count=0&include_available_features=1&include_entities=1&include_new_items_bar=true&interval=30000&latent_count=0',
                [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36',
                    ],
                ]
            );

            //Распаковать json
            $tweets = json_decode($tweets->getBody()->getContents(), true);

//            Log::channel('command_twitter')->info($tweets);

            //Получить список твитов
            $tweets = new Document($tweets['items_html']);
            $tweets = $tweets->find('.js-stream-item');

            if (count($tweets) > 0) {
                $data = [];
                foreach ($tweets as $index => $tweet) {
                    //id твита
                    $data[$index]['id'] = $tweet->getAttribute('data-item-id');
                    //Ссылка на твит
                    $data[$index]['full_url'] = $tweet->first('small.time a')->getAttribute('href');
                    //Аватар
                    $data[$index]['image'] = $tweet->first('img.avatar')->getAttribute('src');
                    //Дата публикации
                    $data[$index]['published'] = $tweet->first('small.time a span')->getAttribute('data-time');
                    //Твит
                    $data[$index]['tweet'] = preg_replace(
                        [
                            '/(.+)pic\.twitter\.com.+/',
                            '/…/',
                            '/https/',
                        ],
                        [
                            '$1',
                            '',
                            '<br/>https',
                        ],
                        $tweet->first('.js-tweet-text-container p')->text());
                }

                $this->tweets = collect($data);
            }
        } catch (GuzzleException $e) {
            \Log::channel('command_twitter')->error([
                'action'  => __METHOD__,
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            exit(1);
        }
    }

    /**
     * Удалить записи с существующими BusinessId
     *
     * @param Tweets $tweets
     *
     * @return int
     */
    private function excludeDuplicates(Tweets $tweets): int
    {
        //Текущий размер коллекции
        $count = $this->tweets->count();

        /** @var Collection $businessIds Получить записи в БД, по BusinessId (если есть) */
        $businessIds = $tweets->whereIn('business_id', $this->tweets->pluck('id'))->get('business_id')->pluck('business_id');

        //Удалить записи с повторяющимися BusinessId
        if ($businessIds->isNotEmpty()) {
            $this->tweets = $this->tweets->filter(function ($value) use ($businessIds) {
                return !$businessIds->contains($value['id']);
            });
        }

        //Вернуть кол-во удаленных записей
        return $count - $this->tweets->count();
    }

    /**
     * Добавить твиты
     *
     * @param Tweets $tweets
     */
    private function addTweets(Tweets $tweets): void
    {
        $tweetsInsert = $this->tweets->map(function ($item) {
            return [
                'business_id'  => $item['id'],
                'full_url'     => $item['full_url'],
                'image'        => $item['image'],
                'body'         => !empty($item['tweet']) ? $item['tweet'] : 'https://twitter.com/marklevinshow/status/' . $item['id'],
                'published_at' => Carbon::createFromTimestamp($item['published'], date_default_timezone_get())
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s'),
            ];
        });

        $tweets->insert($tweetsInsert->reverse()->toArray());
    }
}

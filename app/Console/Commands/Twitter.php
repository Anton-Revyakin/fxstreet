<?php

namespace App\Console\Commands;

use DiDom\Document;
use DiDom\Element;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use PHPUnit\ExampleExtension\Comparable;
use SebastianBergmann\CodeCoverage\Report\PHP;

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
     * @return mixed
     */
    public function handle()
    {
        return $this->getTweetIds();
    }

    private function getTweetIds($tweetId = '1186671140762247168')
    {
        try {
            $tweets = (new Client())->request(
                'GET',
                'https://twitter.com/i/profiles/show/realDonaldTrump/timeline/tweets?composed_count=0&include_available_features=1&include_entities=1&include_new_items_bar=true&interval=30000&latent_count=0&min_position=1189614078534311936',
                [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36',
                    ],
                ]
            );

            $tweets = json_decode($tweets->getBody()->getContents(), true);

            \Log::channel('command_twitter')->info($tweets);

            $tweets = new Document($tweets['items_html']);
            $tweets = $tweets->find('.js-stream-item');

            if (count($tweets) > 0) {
                $data = [];
                foreach ($tweets as $index => $tweet) {
                    echo $index . PHP_EOL;
                    $data[$index]['id'] = $tweet->getAttribute('data-item-id');

                    $data[$index]['tweet'] = preg_replace(
                        [
                            '/<(a|span) .+invisible.+<\/(a|span)>/', //Убрать неотображаемые ссылки
                            '/<a href="([^"]+)"[^>]+>/',             //Очистить ссылки от лишних аттрибутов
                            '/<a href="\/([^"]+")/',                 //Поправить ссылки на твиттер
                            '/<s>@<\/s><b>/',                        //Поправить собачки в ссылках на аккаунты
                            '/ {2,}/',                               //Убрать лишние пробелы
                            '/\n/',                                  //Переносы строк
                        ],
                        [
                            '',
                            ' <a href="$1" target="_blank">',
                            ' <a href="https://twitter.com$1">',
                            '<b>@',
                            ' ',
                            '<br/>',
                        ],
                        $tweet->first('.js-tweet-text-container p'
                        )->innerHtml());
                    \Storage::append('twitter.html', $data[$index]['tweet'] . '<hr/>');
                }
            }
        } catch (GuzzleException $e) {
            \Log::channel('command_twitter')->error([
                'action'  => __METHOD__,
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            exit(1);
        }

        exit(0);

//        try {
//            $response = new Client('https://twitter.com/i/profiles/show/realDonaldTrump/timeline/tweets');
////            $response->s
//
//                ->get(
//                'https://twitter.com/i/profiles/show/realDonaldTrump/timeline/tweets?composed_count=0&include_available_features=1&include_entities=1&include_new_items_bar=true&interval=30000&latent_count=0&min_position=1186671140762247168'
//            );
//            $response = $response->getBody()->getContents();
//            $response = json_decode($response, true);
//            dump($response);
//        } catch (RequestException $e) {
//            \Log::channel('sentrylog')->error($e->getMessage());
//        }
    }
}

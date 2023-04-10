<?php

namespace App\Spiders;

use App\Spiders\Processors\HtmlToMarkdownProcessor;
use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class MediumSpider extends BasicSpider
{
    public array $itemProcessors = [
        HtmlToMarkdownProcessor::class,
    ];

    public array $startUrls = [
        'https://api.rss2json.com/v1/api.json?rss_url=https://medium.com/feed/@hendrikprinsza',
    ];

    protected array $currentItem = [];

    public function parse(Response $response): Generator
    {
        $items = json_decode($response->text(), true)['items'] ?? [];

        $counter = 0;

        /** @var array $item */
        foreach ($items as $item) {
            $counter++;

            // return raw data from feed
            $this->currentItem = $item;

            // return data from post
            yield $this->request('GET', $item['link'], 'parseItem');

            if ($counter >= 1) {
                break;
            }
        }
    }

    public function parseItem(Response $response): Generator
    {
        $title = $response->filter('h1.pw-post-title')->text();
        $article = $response->filter('article')->html();

        $full = $response->getBody();

        yield $this->item(array_merge($this->currentItem, compact(
            'title',
            'article',
            'full'
        )));
    }
}

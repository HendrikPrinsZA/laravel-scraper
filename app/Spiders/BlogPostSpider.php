<?php

namespace App\Spiders;

use App\Spiders\Extensions\ItemDroppedExtension;
use App\Spiders\Processors\HtmlToMarkdownProcessor;
use App\Spiders\Processors\MediumSkipMemberOnlyProcessor;
use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class BlogPostSpider extends BasicSpider
{
    public array $extensions = [
        ItemDroppedExtension::class,
    ];

    public array $itemProcessors = [
        MediumSkipMemberOnlyProcessor::class,
        HtmlToMarkdownProcessor::class,
    ];

    public array $startUrls = [
        'https://medium.com/@soulaimaneyh/javascript-clean-code-tricks-everyone-should-know-feb5690ea597',
        // 'https://medium.com/p/c53db78c014e', // Fetching Tweets in Laravel With Python’s Social Networking Services Scraper
    ];

    public function parse(Response $response): Generator
    {
        $uri = $response->getRequest()->getUri();

        // Regular expression pattern to match "medium.com" or "*.medium.com"
        // - Exception: What if custom subdomain?
        if (preg_match("/^(https?:\/\/)?([\w\.]+\.)*medium\.com/i", $uri)) {
            return $this->parseMedium($response);
        }
    }

    public function parseMedium(Response $response): Generator
    {
        $uri = $response->getRequest()->getUri();
        $title = $response->filter('h1.pw-post-title')->text();
        $article = $response->filter('article')->html();

        $full = $response->getBody();

        yield $this->item(compact(
            'uri',
            'title',
            'article',
            'full'
        ));
    }
}

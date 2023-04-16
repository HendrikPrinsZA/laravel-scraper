<?php

namespace App\Spiders;

use App\Objects\BlogPostObject;
use App\Spiders\Extensions\ItemDroppedExtension;
use App\Spiders\Processors\BlogPostProcessor;
use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use voku\helper\HtmlDomParser;

class BlogPostSpider extends BasicSpider
{
    public array $extensions = [
        ItemDroppedExtension::class,
    ];

    public array $itemProcessors = [
        BlogPostProcessor::class,
    ];

    public array $startUrls = [];

    public function parse(Response $response): Generator
    {
        $uri = $response->getRequest()->getUri();

        // Regular expression pattern to match "medium.com" or "*.medium.com"
        // - Exception: What if custom subdomain?
        if (preg_match("/^(https?:\/\/)?([\w\.]+\.)*medium\.com/i", $uri)) {
            return $this->parseMedium($response);
        }

        // TODO: Throw exception here!
    }

    /**
     * Parse the raw medium post
     */
    public function parseMedium(Response $response): Generator
    {
        $html = $response->getBody();
        $dom = HtmlDomParser::str_get_html($html);

        $tags = [];
        $tagElements = $dom->findMulti('.ks.aw.al.an');
        foreach ($tagElements as $tagElement) {
            $tags[] = $tagElement->text();
        }

        yield $this->item(BlogPostObject::create([
            'uri' => $response->getRequest()->getUri(),
            'title' => $response->filter('h1.pw-post-title')->text(),
            'html' => $html,
            'htmlArticle' => $response->filter('section')->html(),
            'tags' => $tags,
        ]));
    }
}

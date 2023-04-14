<?php

namespace App\Spiders\Processors;

use App\Services\BlogPostService;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class BlogPostProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function __construct(protected BlogPostService $blogPostService) { }

    public function processItem(ItemInterface $blogPost): ItemInterface
    {
        /** @var \App\Objects\BlogPostObject $blogPost */
        if ($this->blogPostService->isBehindPaywall($blogPost)) {
            return $blogPost->drop('Behind member-only paywall');
        }

        $blogPost = $this->blogPostService->htmlToMarkdown($blogPost);

        $blogPost = $this->blogPostService->review($blogPost);

        return $blogPost;
    }
}

<?php

namespace App\Actions\Scrapers;

use App\Services\BlogPostService;
use App\Spiders\BlogPostSpider;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use RoachPHP\Roach;

class FetchBlogPosts
{
    use AsAction;

    public string $commandSignature = 'scraper:fetch-blog-posts';

    public string $commandDescription = 'Fetch blog posts';

    public function __construct(protected BlogPostService $blogPostService) { }

    public function asCommand(Command $command): void
    {
        $blogPosts = Roach::collectSpider(BlogPostSpider::class);

        /** @var \App\Objects\BlogPostObject $blogPost */
        foreach ($blogPosts as $blogPost) {
            $command->info(sprintf('URI: %s', $blogPost->uri));
            $command->line(sprintf("```md\n%s\n```", $blogPost->markdown));
            $command->info(sprintf('Feedback: %s', $blogPost->feedback));
        }
    }
}

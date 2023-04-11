<?php

namespace App\Actions\Scrapers;

use App\Spiders\BlogPostSpider;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use RoachPHP\Roach;

class FetchBlogPosts
{
    use AsAction;

    public string $commandSignature = 'scraper:fetch-blog-posts';

    public string $commandDescription = 'Fetch blog posts';

    public function asCommand(Command $command): void
    {
        $items = Roach::collectSpider(BlogPostSpider::class);

        foreach ($items as $item) {
            $command->info(sprintf('URI: %s', $item['uri']));
            $command->line(sprintf("```md\n%s\n```", $item['markdown']));
        }
    }
}

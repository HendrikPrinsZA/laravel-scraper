<?php

namespace App\Actions\Scrapers;

use App\Spiders\MediumSpider;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use RoachPHP\Roach;

class FetchMediumPosts
{
    use AsAction;

    public string $commandSignature = 'scraper:fetch-medium-posts';

    public string $commandDescription = 'Fetch latest Medium posts';

    public function asCommand(Command $command): void
    {
        $items = Roach::collectSpider(MediumSpider::class);

        foreach ($items as $item) {
            print_r([
                'title' => $item['title'],
                'description' => $item['description'],
                'markdown' => $item['markdown'],
            ]);
        }
    }
}

<?php

namespace App\Actions\Scrapers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchRedditPosts
{
    use AsAction;

    protected const MAX_COUNT = 100;

    protected const PERIOD = 'month';

    protected const SUBREDDITS = [
        'r/PHP',
        'r/laravel',
    ];

    public string $commandSignature = 'scraper:fetch-reddit-posts';

    public string $commandDescription = 'Fetch top posts from Reddit for the last month';

    protected ?Command $command = null;

    protected string $filepath;

    public function __construct()
    {
        $this->filepath = sprintf(
            '%s-%s.md',
            str_replace(':', '/', $this->commandSignature),
            now()->format('Ymd')
        );
    }

    public function asCommand(Command $command): void
    {
        $this->command = $command;
        $this->handle();
    }

    public function handle(): void
    {
        $this->command->info($this->commandDescription);
        $this->command->newLine();

        $this->clear();
        $this->append('# What happened on Reddit?');
        $this->append("There are quite a few relevant subreddits, but we'll focus on the 2 most popular and active ones to start with. To narrow the scope, we'll restrict the posts to links only.");
        $this->newLine();

        foreach (self::SUBREDDITS as $subreddit) {
            $this->fetch($subreddit);
        }
    }

    private function fetch(string $subreddit): void
    {
        $subredditUrl = sprintf('https://www.reddit.com/%s/top/.json', $subreddit);

        $this->append(sprintf('## Subreddit %s', $subreddit));
        $this->append(sprintf(
            'Top 10 posts in the <a href="%s">%s</a> community.',
            $subredditUrl,
            $subreddit
        ));
        $this->newLine();

        $response = Http::acceptJson()->get($subredditUrl, [
            'count' => self::MAX_COUNT,
            't' => self::PERIOD,
        ]);

        // Only consider links
        $posts = collect($response->json('data')['children'])
            ->filter(fn ($post) => ! empty(data_get($post, 'data.url_overridden_by_dest')))
            ->values();

        foreach ($posts as $idx => $post) {
            $postLink = sprintf(
                'https://www.reddit.com/r/PHP/comments/%s/',
                data_get($post, 'data.id')
            );

            $this->append(sprintf(
                '**#%d) <a href="%s">%s</a>**',
                ($idx + 1),
                $postLink,
                data_get($post, 'data.title')
            ));
            $this->newLine();
            $this->append(data_get($post, 'data.url_overridden_by_dest'));
            $this->newLine();

            if ($idx >= 9) {
                break;
            }
        }
    }

    private function append(string $line): void
    {
        $this->command->line($line);
        Storage::append($this->filepath, $line);
    }

    private function newLine(): void
    {
        $line = "\n";
        $this->append($line);
    }

    private function clear(): void
    {
        if (Storage::exists($this->filepath)) {
            Storage::delete($this->filepath);
        }
    }
}

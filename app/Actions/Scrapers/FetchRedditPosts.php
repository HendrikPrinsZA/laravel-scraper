<?php

namespace App\Actions\Scrapers;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchRedditPosts
{
    use AsAction;

    public const HOST = 'https://www.reddit.com';

    public const SUBREDDITS = [
        'r/PHP',
        'r/laravel',
    ];

    protected const MAX_COUNT = 100;

    protected const PERIOD = 'month';

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
        $this->command?->info($this->commandDescription);
        $this->command?->newLine();

        $this->clear();
        $out = $this->fetch();
        $this->append($out);

        $this->command?->info(sprintf('Saved file at ./storage/app/%s', $this->filepath));
    }

    public function fetch(): string
    {
        $lines = collect();

        $lines->push('# What happened on Reddit?');
        $lines->push("There are quite a few relevant subreddits, but we'll focus on the 2 most popular and active ones to start with. To narrow the scope, we'll restrict the posts to links only.");
        $lines->push('');
        foreach (self::SUBREDDITS as $subreddit) {
            $lines->push(...$this->fetchSubreddit($subreddit));
        }

        return $lines->join("\n");
    }

    public function fetchSubreddit(string $subreddit): Collection
    {
        $subredditUrl = sprintf('%s/%s/top/.json', self::HOST, $subreddit);

        $lines = collect();
        $lines->push(sprintf('## Subreddit <a href="%s">%s</a>', $subredditUrl, $subreddit));
        $lines->push(sprintf(
            'Top 10 posts in the <a href="%s">%s</a> community.',
            $subredditUrl,
            $subreddit
        ));
        $lines->push('');

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

            $lines->push(sprintf(
                '**#%d) <a href="%s">%s</a>**',
                ($idx + 1),
                $postLink,
                data_get($post, 'data.title')
            ));
            $lines->push('');
            $lines->push(data_get($post, 'data.url_overridden_by_dest'));
            $lines->push('');

            if ($idx >= 9) {
                break;
            }
        }

        return $lines;
    }

    private function append(string $line): void
    {

    }

    private function clear(): void
    {
        if (Storage::exists($this->filepath)) {
            Storage::delete($this->filepath);
        }
    }
}

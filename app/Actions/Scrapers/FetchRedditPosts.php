<?php

namespace App\Actions\Scrapers;

use Carbon\Carbon;
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
        $this->append("There are quite a few relevant subreddits, but we'll focus on the 2 most popular and active ones to start with. To narrow the scope further, we'll restrict the posts to links only. Based on my initial investigation this should show the most value.");
        $this->append("\n");

        foreach (self::SUBREDDITS as $subreddit) {
            $this->fetch($subreddit);
        }
    }

    private function append(string $line): void
    {
        $this->command->line($line);
        Storage::append($this->filepath, $line);
    }

    private function clear(): void
    {
        if (Storage::exists($this->filepath)) {
            Storage::delete($this->filepath);
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

        $response = Http::acceptJson()->get($subredditUrl, [
            'count' => self::MAX_COUNT,
            't' => self::PERIOD,
        ]);

        // Only consider links
        $posts = collect($response->json('data')['children'])
            ->filter(fn ($post) => ! empty(data_get($post, 'data.url_overridden_by_dest')))
            ->values();

        $this->append("\n");

        // Display as list
        foreach ($posts as $idx => $post) {
            $created = Carbon::createFromTimestamp(data_get($post, 'data.created'));

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
            $this->append(sprintf('- Link: %s', data_get($post, 'data.url_overridden_by_dest')));
            $this->append(sprintf(
                '- Votes: +%d/-%d (%d%%)',
                data_get($post, 'data.ups'),
                data_get($post, 'data.downs'),
                floatval(data_get($post, 'data.upvote_ratio')) * 100,
            ));

            $author = data_get($post, 'data.author');
            $this->append(sprintf(
                '- Posted on %s by <a href="https://www.reddit.com/user/%s">@%s</a>',
                $created->format('Y-m-d'),
                $author,
                $author
            ));
            $this->append("\n");

            if ($idx >= 9) {
                break;
            }
        }
    }
}

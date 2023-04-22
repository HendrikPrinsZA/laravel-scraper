<?php

namespace App\Actions\Scrapers;

use App\Collections\RedditPostObjectCollection;
use App\Objects\RedditPostObject;
use Illuminate\Console\Command;
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
        $out = $this->getMarkdownText();
        $this->write($out);

        $this->command?->info(sprintf('Saved file at ./storage/app/%s', $this->filepath));
    }

    public function getMarkdownText(): string
    {
        $lines = collect();

        $lines->push('# What happened on Reddit?');
        $lines->push("There are quite a few relevant subreddits, but we'll focus on the 2 most popular and active ones to start with. To narrow the scope, we'll restrict the posts to links only.");
        $lines->push('');
        foreach (self::SUBREDDITS as $subreddit) {
            $lines->push(sprintf(
                '## Subreddit <a href="%s/%s">%s</a>',
                self::HOST,
                $subreddit,
                $subreddit
            ));

            $lines->push('Top 10 posts in the past month.');
            $lines->push('');

            $this->fetchPosts($subreddit)->slice(0, 10)
                ->each(function (RedditPostObject $post, int $index) use ($lines) {
                    $lines->push(sprintf(
                        '**#%d) <a href="%s">%s</a>**',
                        ($index + 1),
                        $post->uri,
                        $post->title
                    ));
                    $lines->push('');
                    $lines->push($post->targetUri);
                    $lines->push('');
                });
        }

        return $lines->join("\n");
    }

    public function fetchPosts(string $subreddit): RedditPostObjectCollection
    {
        $subredditUrl = sprintf('%s/%s/top/.json', self::HOST, $subreddit);

        $response = Http::acceptJson()->get($subredditUrl, [
            'count' => self::MAX_COUNT,
            't' => self::PERIOD,
        ]);

        // Only consider links
        $posts = collect($response->json('data')['children'])
            ->filter(fn ($post) => ! empty(data_get($post, 'data.url_overridden_by_dest')))
            ->values();

        $redditPosts = RedditPostObjectCollection::make();
        $posts->each(fn ($post) => $redditPosts->push(RedditPostObject::make([
            'uri' => sprintf('%s/r/PHP/comments/%s/', self::HOST, data_get($post, 'data.id')),
            'title' => data_get($post, 'data.title'),
            'targetUri' => data_get($post, 'data.url_overridden_by_dest'),
        ]))
        );

        return $redditPosts;
    }

    private function write(string $text): void
    {
        Storage::append($this->filepath, $text);
    }

    private function clear(): void
    {
        if (Storage::exists($this->filepath)) {
            Storage::delete($this->filepath);
        }
    }
}

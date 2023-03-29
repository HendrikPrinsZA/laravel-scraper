<?php

namespace App\Actions\Scrapers;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchRedditPosts
{
    use AsAction;

    protected const MAX_COUNT = 100;

    protected const PERIOD = 'month';

    public string $commandSignature = 'scraper:fetch-reddit-posts';

    public string $commandDescription = 'Fetch top posts from https://reddit.com/r/PHP for the last month';

    public function asCommand(Command $command): void
    {
        $this->handle($command);
    }

    public function handle(Command $command): void
    {
        $command->info($this->commandDescription);
        $command->newLine();

        $this->fetch($command);
    }

    private function fetch(Command $command): void
    {
        $response = Http::acceptJson()->get('https://www.reddit.com/r/PHP/top/.json', [
            'count' => self::MAX_COUNT,
            't' => self::PERIOD,
        ]);

        $posts = collect($response->json('data')['children']);
        if ($posts->isEmpty()) {
            $command->info('Nothing to process');

            return;
        }

        // Only consider links
        $posts = $posts
            ->filter(fn ($post) => ! empty(data_get($post, 'data.url_overridden_by_dest')))
            ->values();

        // Display as list
        $posts->each(function ($post, $idx) use ($command) {
            $created = Carbon::createFromTimestamp(data_get($post, 'data.created'));

            $command->info(sprintf('#%d) %s', ($idx + 1), data_get($post, 'data.title')));
            $command->line(sprintf('- URL: %s', data_get($post, 'data.url_overridden_by_dest')));
            $command->line(sprintf(
                '- Votes: +%d/-%d (%d%%)',
                data_get($post, 'data.ups'),
                data_get($post, 'data.downs'),
                floatval(data_get($post, 'data.upvote_ratio')) * 100,
            ));
            $command->line(sprintf('- Date: %s', $created->format('Y-m-d H:i:s')));
            $command->newLine();
        });
    }
}

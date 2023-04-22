<?php

namespace App\Actions\Scrapers;

use App\Utilities\ShellCommand;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchTwitterPosts
{
    use AsAction;

    public string $commandSignature = 'scraper:fetch-twitter-posts';

    public string $commandDescription = 'Fetch top posts from Twitter for the last month';

    protected ?Command $command = null;

    protected string $filepath;

    protected string $filepathLog;

    public function __construct()
    {
        $this->filepath = sprintf(
            '%s-%s.md',
            str_replace(':', '/', $this->commandSignature),
            now()->format('Ymd')
        );

        $this->filepathLog = str_replace('.md', '.log', $this->filepath);
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
        $this->append('# What happened on Twitter?');
        $this->append('Most popular tweets based on the following hashtags: #php #laravel');
        $this->newLine();

        $this->fetch();
    }

    private function fetch(): void
    {
        $tweets = $this->fetchTweets();

        $tweets = $tweets
            ->unique('url')
            ->sortByDesc('likeCount')
            ->values();

        foreach ($tweets as $idx => $tweet) {
            if ($tweet['likeCount'] < 5) {
                break;
            }

            if ($idx > 20) {
                break;
            }

            $this->append($tweet['url']);
            $this->newLine();
        }
    }

    private function fetchTweets(int $minFaves = 200): Collection
    {
        $this->command->info(sprintf(
            'Fetching tweets with minimum likes of %d',
            $minFaves
        ));

        $filepathLogAbs = Storage::path($this->filepathLog);
        $commandArgs = [
            'snscrape',
            '--jsonl',
            '--progress',
            '--max-results 100',
            sprintf(
                'twitter-search "#php #laravel since:%s until:%s min_faves:%d"',
                now()->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d'),
                $minFaves
            ),
        ];

        $command = sprintf(
            '%s >> %s',
            implode(' ', $commandArgs),
            $filepathLogAbs
        );

        $this->command->info(sprintf('Executing: %s', $command));

        try {
            ShellCommand::execute($command);
        } catch (Exception $e) {
            throw new Exception(sprintf(
                'Unable to fetch tweets with snscrape, probably due to %s',
                'https://github.com/JustAnotherArchivist/snscrape/issues/846'
            ));
        }

        if ($minFaves > 1) {
            sleep(60);

            if ($minFaves > 100) {
                $minFaves -= 100;
            } else {
                $minFaves -= 10;
            }

            return $this->fetchTweets($minFaves);
        }

        $tweets = collect();
        $lines = Storage::get($this->filepathLog);
        foreach (explode("\n", $lines) as $line) {
            if (empty($line)) {
                continue;
            }

            $tweets->push(json_decode($line, true));
        }

        return $tweets;
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
        if (Storage::exists($this->filepathLog)) {
            Storage::delete($this->filepathLog);
        }

        if (Storage::exists($this->filepath)) {
            Storage::delete($this->filepath);
        }
    }
}

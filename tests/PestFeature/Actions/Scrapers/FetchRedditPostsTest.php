<?php

use App\Actions\Scrapers\FetchRedditPosts;

it('can fetch reddit posts', function (string $subreddit, string $filename) {
    $filepath = sprintf('%s/Files/%s', __DIR__, $filename);
    setupMockResponse(FetchRedditPosts::HOST, getFileContents($filepath));

    $response = FetchRedditPosts::make()->fetch($subreddit);
    expect($response)
        ->not->toBeEmpty()
        ->toBeString();

    expect(count(explode("\n", $response)))
        ->toBeGreaterThan(2);
})->with([
    [FetchRedditPosts::SUBREDDITS[0], 'reddit-php.json'], // https://www.reddit.com/r/PHP/top/.json?t=month'
    [FetchRedditPosts::SUBREDDITS[1], 'reddit-laravel.json'], // https://www.reddit.com/r/laravel/top/.json?t=month'
]);

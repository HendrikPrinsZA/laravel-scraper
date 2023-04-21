<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/
uses(TestCase::class)->beforeAll(function () {
    // Runs before each file...

})->beforeEach(function () {
    // Runs before each test...

})->afterEach(function () {
    // Runs after each test...

})->afterAll(function () {
    // Runs after each file...

})->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/
function asUser(): TestCase
{
    $user = User::firstWhere('email', 'test@example.com');

    if (is_null($user)) {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    return test()->actingAs($user);
}

function setupMockResponse(string $url, array $response): void
{
    Http::fake([
        sprintf('%s*', $url) => $response,
    ]);
}

function getFileContents(string $filepath): mixed
{
    expect($filepath)->toBeReadableFile();

    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    $data = match ($extension) {
        'json' => json_decode(file_get_contents($filepath), true),
        default => null
    };

    if (is_null($data)) {
        throw new Exception(sprintf('Unable to parse file: %s', $filepath));
    }

    return $data;
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

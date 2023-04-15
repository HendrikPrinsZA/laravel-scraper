<?php

namespace App\Actions\Scrapers;

use App\Actions\Action;
use App\Actions\Resources\BlogPostResource;
use App\Objects\BlogPostObject;
use App\Services\BlogPostService;
use App\Spiders\BlogPostSpider;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Resources\Json\JsonResource;
use Lorisleiva\Actions\ActionRequest;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;

class FetchBlogPost extends Action
{
    public string $commandSignature = 'scraper:fetch-blog-posts';

    public string $commandDescription = 'Fetch blog posts';

    public function __construct(protected BlogPostService $blogPostService)
    {
    }

    public function asCommand(Command $command): void
    {
        $url = $command->ask('URL', 'https://medium.com/@soulaimaneyh/php-clean-code-tricks-everyone-should-know-afd406bd00bc');

        $blogPost = $this->process($url);
        $command->info(sprintf('URI: %s', $blogPost->uri));
        $command->line(sprintf("```md\n%s\n```", $blogPost->markdown));
        $command->info(sprintf("Feedback\n%s\n\n", $blogPost->feedback));
        $command->info(sprintf("Suggestions\n%s\n\n", $blogPost->suggestions));
    }

    public function asController(ActionRequest $request): JsonResource
    {
        $url = $request->url ?? null;

        if (is_null($url)) {
            return $this->error('Expected parameter "url" not found');
        }

        return BlogPostResource::make($this->process($request->url));
    }

    // Tried, but doesn't work?!?
    // - Not sold on this actions strategy!
    // public function rules(): array
    // {
    //     return [
    //         'url' => ['required'],
    //     ];
    // }

    protected function process(string $url): BlogPostObject
    {
        $blogPosts = Roach::collectSpider(BlogPostSpider::class, new Overrides(
            startUrls: [$url]
        ));

        if (empty($blogPosts)) {
            throw new Exception('Expected at least one blog post to be returned');
        }

        return $blogPosts[0];
    }
}

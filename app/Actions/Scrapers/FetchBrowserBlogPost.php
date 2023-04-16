<?php

namespace App\Actions\Scrapers;

use App\Actions\Action;
use App\Browsers\MediumBrowser;
use App\Objects\BlogPostObject;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class FetchBrowserBlogPost extends Action
{
    public string $commandSignature = 'scraper:fetch-browser-blog-posts';

    public string $commandDescription = 'Fetch blog posts with browser';

    protected HttpBrowser $browser;

    public function __construct()
    {
        $this->browser = new MediumBrowser(HttpClient::create());
    }

    public function asCommand(Command $command): void
    {
        // $url = $command->ask('URL', 'https://medium.com/@soulaimaneyh/php-clean-code-tricks-everyone-should-know-afd406bd00bc');

        $url = 'https://medium.com/@soulaimaneyh/php-clean-code-tricks-everyone-should-know-afd406bd00bc';
        $blogPost = $this->process($url);
        $command->info(sprintf('URI: %s', $blogPost->uri));
        $command->line(sprintf("```md\n%s\n```", $blogPost->markdown));
        $command->info(sprintf("Feedback\n%s\n\n", $blogPost->feedback));
        $command->info(sprintf("Suggestions\n%s\n\n", $blogPost->suggestions));
    }

    protected function process(string $url): BlogPostObject
    {
        // $crawler = $this->browser->request('GET', 'https://hendrik.free.beeceptor.com'); // Mock testing
        $crawler = $this->browser->request('GET', 'https://medium.com/me/stats');

        $html = $crawler->html();
        // $link = $crawler->selectLink('Audience stats')->link();

        return BlogPostObject::make([
            'uri' => $url,
            'html' => $html,
        ]);
    }
}

<?php

namespace App\Services;

use App\Objects\BlogPostObject;
use League\HTMLToMarkdown\HtmlConverter;

/**
 * Functionality for blog posts
 *
 * For example)
 * - Review article with OpenAI
 * - Generate improved titles
 * - Generate header image with Dalle-2 (or midjourney)
 *   - Generate the prompt with OpenAI GPT-4
 */
class BlogPostService
{
    public function __construct(
        protected HtmlConverter $htmlConverter,
        protected OpenAIService $openAIService
    ) {
        $this->htmlConverter->setOptions([
            'strip_tags' => true,
            'strip_placeholder_links' => true,
            'hard_break' => true,
            'remove_nodes' => 'figcaption',
        ]);
    }

    public function isBehindPaywall(BlogPostObject $blogPost): bool
    {
        return str_contains($blogPost->htmlArticle, 'Member-only');
    }

    public function htmlToMarkdown(BlogPostObject $blogPost): BlogPostObject
    {
        $markdown = $this->htmlConverter->convert($blogPost->htmlArticle);

        // replace empty images (waiting for js)
        $markdown = str_replace('![]()', '', $markdown);

        // replace multi empty lines
        $markdown = preg_replace('/\n(\s*\n){2,}/', "\n\n", $markdown);

        $blogPost->markdown = $markdown;

        return $blogPost;
    }

    public function review(BlogPostObject $blogPost): BlogPostObject
    {
        $blogPost->feedback = $this->openAIService
            ->getFeedback($blogPost->markdown, $blogPost->tags);

        $blogPost->suggestions = $this->openAIService
            ->getTitleSuggestions($blogPost->markdown, $blogPost->tags);

        return $blogPost;
    }
}

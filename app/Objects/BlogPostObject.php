<?php

namespace App\Objects;

/**
 * Blog Post Object
 *
 * @property string $uri
 * @property string $html
 * @property string $title
 * @property array $tags
 * @property string $htmlArticle
 * @property string $markdown
 * @property string $feedback
 * @property string $suggestions
 */
class BlogPostObject extends BaseObject
{
    public string $uri = '';

    public string $html = '';

    public string $title = '';

    public array $tags = [];

    public string $htmlArticle = '';

    public string $markdown = '';

    public string $feedback = '';

    public string $suggestions = '';
}

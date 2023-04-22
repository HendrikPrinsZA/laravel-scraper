<?php

namespace App\Objects;

use App\Traits\HasCollection;

/**
 * Reddit Post Object
 *
 * @property string $uri
 * @property string $title
 * @property string $targetUri
 */
class RedditPostObject extends BaseObject
{
    use HasCollection;

    public string $uri = '';

    public string $title = '';

    public string $targetUri = '';
}

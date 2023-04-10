<?php

namespace App\Spiders\Processors;

use League\HTMLToMarkdown\HtmlConverter;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class HtmlToMarkdownProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function __construct(protected HtmlConverter $htmlConverter)
    {
        $this->htmlConverter->setOptions([
            'strip_tags' => true,
            'strip_placeholder_links' => true,
            'hard_break' => true,
            'remove_nodes' => 'figcaption',
        ]);
    }

    public function processItem(ItemInterface $item): ItemInterface
    {
        $key = $this->option('key');
        $html = $item->get($key);

        $markdown = $this->htmlConverter->convert($html);

        // replace empty images (waiting for js)
        $markdown = str_replace('![]()', '', $markdown);

        // replace multi empty lines
        $markdown = preg_replace('/\n(\s*\n){2,}/', "\n\n", $markdown);

        $item->set('markdown', $markdown);

        return $item;
    }

    private function defaultOptions(): array
    {
        return [
            'key' => 'article',
        ];
    }
}

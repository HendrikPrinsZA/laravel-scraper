<?php

namespace App\Spiders\Processors;

use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class MediumSkipMemberOnlyProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $html = $item->get('article');

        if (str_contains($html, 'Member-only')) {
            return $item->drop('Behind member-only paywall');
        }

        return $item;
    }
}

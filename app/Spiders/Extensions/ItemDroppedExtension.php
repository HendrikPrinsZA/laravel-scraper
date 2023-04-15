<?php

namespace App\Spiders\Extensions;

use Exception;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\App;
use RoachPHP\Events\ItemDropped;
use RoachPHP\Extensions\ExtensionInterface;
use RoachPHP\Support\Configurable;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class ItemDroppedExtension implements ExtensionInterface
{
    use Configurable, InteractsWithIO {
        Configurable::option insteadof InteractsWithIO;
        InteractsWithIO::option as commandOption;
    }

    protected bool $runningInConsole = false;

    public function __construct()
    {
        if (App::runningInConsole()) {
            $this->setOutput(new OutputStyle(
                new StringInput(''),
                new ConsoleOutput()
            ));

            $this->runningInConsole = true;

            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ItemDropped::NAME => ['onItemDropped', 100],
        ];
    }

    public function onItemDropped(ItemDropped $event): void
    {
        $this->error(sprintf(
            "Dropped item: %s\n- Reason: %s",
            $event->item->get('title'),
            $event->item->getDropReason(),
        ));
    }

    protected function error(string $message): void
    {
        if (! $this->runningInConsole) {
            throw new Exception('Only supported in terminal at the moment, more work needed');
        }

        $this->warn($message);
    }
}

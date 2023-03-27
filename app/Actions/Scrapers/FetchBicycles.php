<?php

namespace App\Actions\Scrapers;

use App\Models\Bicycle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchBicycles
{
    use AsAction;

    public string $commandSignature = 'scraper:fetch-bicycles';

    public string $commandDescription = 'Fetch impounded bicycles from www.verlorenofgevonden.nl';

    public function asCommand(Command $command): void
    {
        $this->handle($command);
    }

    public function handle(Command $command): void
    {
        $this->fetch($command, now()->subMonth(), now());
    }

    private function fetch(
        Command $command,
        Carbon $dateFrom,
        Carbon $dateTo,
        int $from = 0
    ): void {
        $response = Http::acceptJson()->get('https://verlorenofgevonden.nl/scripts/ez.php', [
            'site' => 'nl',
            'q' => 'fietsendepot',
            'date_from' => $dateFrom->format('d-m-Y'),
            'date_to' => $dateTo->format('d-m-Y'),
            'timestamp' => now()->timestamp,
            'from' => $from,
        ]);

        $hits = collect($response->json('hits.hits'));
        if ($hits->isEmpty()) {
            $command->info('Done processing');

            return;
        }

        $upserts = collect();
        foreach ($hits as $hit) {
            $registeredAt = Carbon::parse(data_get($hit, '_source.RegistrationDate'));
            $upserts->push([
                'object_number' => data_get($hit, '_source.ObjectNumber'),
                'type' => data_get($hit, '_source.Category'),
                'sub_type' => data_get($hit, '_source.SubCategory'),
                'brand' => data_get($hit, '_source.Brand'),
                'color' => data_get($hit, '_source.Color'),
                'description' => data_get($hit, '_source.Description'),
                'city' => data_get($hit, '_source.City'),
                'storage_location' => data_get($hit, '_source.StorageLocation.Name'),
                'registered_at' => $registeredAt,
            ]);
        }

        Bicycle::upsert($upserts->toArray(), ['object_number'], [
            'type',
            'sub_type',
            'brand',
            'color',
            'description',
            'city',
            'storage_location',
            'registered_at',
        ]);

        $total = $from + $upserts->count();
        $command->info(sprintf('Processed %d results', $total));
        $this->fetch($command, $dateFrom, $dateTo, $total);
    }
}

<?php

namespace Database\Seeders\Enums;

use App\Collections\CountryCollection;
use App\Enums\CountryCode;
use App\Models\Country;

class CountryCodeSeeder extends EnumSeeder
{
    public function seed(): void
    {
        $countries = CountryCollection::make();
        CountryCode::all()->each(
            fn (array $details) => $countries->push(Country::factory()->make($details))
        );

        $countries->upsert();
    }

    protected function cleanup(): void
    {
        Country::whereNotIn('code', CountryCode::cases())->delete();
    }
}

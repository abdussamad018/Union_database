<?php

namespace App\Providers;

use App\Models\CustomFieldDefinition;
use App\Models\Donation;
use App\Models\Facility;
use App\Models\House;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'resident' => Resident::class,
            'house' => House::class,
            'donation' => Donation::class,
            'facility' => Facility::class,
        ]);
    }
}

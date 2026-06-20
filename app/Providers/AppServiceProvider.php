<?php

namespace App\Providers;

use App\Models\CustomFieldDefinition;
use App\Models\Donation;
use App\Models\Facility;
use App\Models\House;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            $request = request();

            if ($request->header('X-Forwarded-Proto') === 'https' || $request->isSecure()) {
                URL::forceScheme('https');
            }

            $appUrl = config('app.url');
            if (empty($appUrl) || str_contains($appUrl, 'localhost')) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());
            }
        }

        Relation::enforceMorphMap([
            'resident' => Resident::class,
            'house' => House::class,
            'donation' => Donation::class,
            'facility' => Facility::class,
        ]);
    }
}

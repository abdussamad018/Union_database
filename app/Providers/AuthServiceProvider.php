<?php

namespace App\Providers;

use App\Models\CustomFieldDefinition;
use App\Models\Donation;
use App\Models\Resident;
use App\Policies\CustomFieldDefinitionPolicy;
use App\Policies\DonationPolicy;
use App\Policies\ResidentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Resident::class => ResidentPolicy::class,
        Donation::class => DonationPolicy::class,
        CustomFieldDefinition::class => CustomFieldDefinitionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}

<?php

    namespace App\Providers;

    use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
    use Illuminate\Support\Facades\Gate;
    use App\Models\Standard\Standard;
    use App\Policies\StandardPolicy;

    class AuthServiceProvider extends ServiceProvider
    {
        /**
         * The model to policy mappings for the application.
         *
         * @var array<class-string, class-string>
         */
        protected $policies = [
            Standard::class => StandardPolicy::class,
        ];

        /**
         * Register any authentication / authorization services.
         */
        public function boot(): void
        {
            $this->registerPolicies();

            // Optional: Define additional gates if needed
            Gate::define('create_standard', [StandardPolicy::class, 'create']);
        }
    }


<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        Gate::define('edit-article', 'App\Policies\ArticlePolicy@edit');
        Gate::define('delete-article', 'App\Policies\ArticlePolicy@delete');

        Gate::define('edit-comment', 'App\Policies\CommentPolicy@edit');
        Gate::define('delete-comment', 'App\Policies\CommentPolicy@delete');
    }
}

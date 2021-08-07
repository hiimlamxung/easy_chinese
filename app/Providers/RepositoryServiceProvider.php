<?php

namespace App\Providers;

use App\Repositories\Admins\AdminRepository;
use App\Repositories\Admins\Contract\AdminRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            AdminRepositoryInterface::class,
            AdminRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

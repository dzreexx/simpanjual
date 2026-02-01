<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // dd([
        //     'db_default' => config('database.default'),
        //     'session_driver' => config('session.driver'),
        //     'session_connection' => config('session.connection'),
        //     'env_db_conn' => env('DB_CONNECTION'),
        // ]);

        // View::composer('partials.sidebar', function ($view) {
        //     $view->with('brands', \App\Models\Brands::all());
        // });
        // View::share('brands', \App\Models\Brands::orderBy('brand_name', 'asc')->get());
        if (Schema::connection('mysql')->hasTable('brands')) {
            $brands = \App\Models\Brands::on('mysql')->orderBy('brand_name')->get();
            view()->share('brands', $brands);
        }
    }
}

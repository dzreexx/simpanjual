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
        // View::composer('partials.sidebar', function ($view) {
        //     $view->with('brands', \App\Models\Brands::all());
        // });
        // View::share('brands', \App\Models\Brands::orderBy('brand_name', 'asc')->get());
        if (Schema::hasTable('brands')) {
            $brands = \App\Models\Brands::orderBy('brand_name')->get();
            view()->share('brands', $brands);
        }
    }
}

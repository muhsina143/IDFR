<?php

namespace App\Providers;

use App\Models\Season;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            if (Schema::hasTable('seasons')) {
                $currentSeason = Season::where('status', 'published')
                    ->orderByDesc('number')
                    ->first() ?? Season::orderByDesc('number')->first();

                $allSeasons = Season::where('status', 'published')
                    ->orderBy('number', 'asc')
                    ->get();

                $view->with('currentSeason', $currentSeason)
                    ->with('allSeasons', $allSeasons);
            }
        });
    }
}

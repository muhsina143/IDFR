<?php

namespace App\Providers;

use App\Models\ProfessionalCategory;
use App\Models\Season;
use App\Models\SponsorshipEnquiry;
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
                $hasSession = false;
                try {
                    $hasSession = request()->hasSession();
                } catch (\Throwable $e) {
                    $hasSession = false;
                }

                $currentSeason = Season::current();

                if ($currentSeason && $hasSession) {
                    session([
                        'selected_season_id' => $currentSeason->id,
                        'selected_season_num' => $currentSeason->number,
                    ]);
                }

                $allSeasons = Season::where('status', 'published')
                    ->orderBy('number', 'asc')
                    ->get();

                $view->with('currentSeason', $currentSeason)
                    ->with('allSeasons', $allSeasons);
            }

            if (Schema::hasTable('sponsorship_enquiries')) {
                $view->with('sponsorshipTypes', SponsorshipEnquiry::availableTypes());
                if (str_starts_with($view->getName(), 'admin.')) {
                    $view->with('newSponsorshipCount', SponsorshipEnquiry::where('status', 'new')->count());
                }
            }

            if (Schema::hasTable('professional_categories')) {
                $navCategories = ProfessionalCategory::where('is_active', true)
                    ->orderBy('display_order')
                    ->get();
                $view->with('navCategories', $navCategories);
            }
        });
    }
}

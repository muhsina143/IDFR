<?php

namespace App\Providers;

use App\Models\Asdreams\AsdreamsBanner;
use App\Models\Asdreams\AsdreamsBrand;
use App\Models\Asdreams\AsdreamsContactEnquiry;
use App\Models\Asdreams\AsdreamsOfferEnquiry;
use App\Models\Asdreams\AsdreamsShootBooking;
use App\Models\ProfessionalCategory;
use App\Models\Season;
use App\Models\SponsorshipEnquiry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production') || str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

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
            }

            if (Schema::hasTable('professional_categories')) {
                $navCategories = ProfessionalCategory::where('is_active', true)
                    ->orderBy('display_order')
                    ->get();
                $view->with('navCategories', $navCategories);
            }

            if (Schema::hasTable('asdreams_banners')) {
                $asdBanners = AsdreamsBanner::active()->get();
                $asdBrands = AsdreamsBrand::active()->get();
                $view->with('asdBanners', $asdBanners)
                    ->with('asdBrands', $asdBrands);
            }
        });

        View::composer('admin.layouts.app', function ($view): void {
            if (Schema::hasTable('sponsorship_enquiries')) {
                $view->with('newSponsorshipCount', SponsorshipEnquiry::where('status', 'new')->count());
            }

            $offerUnread = Schema::hasTable('asdreams_offer_enquiries')
                ? AsdreamsOfferEnquiry::unread()->count()
                : 0;
            $contactUnread = Schema::hasTable('asdreams_contact_enquiries')
                ? AsdreamsContactEnquiry::unread()->count()
                : 0;

            if (Schema::hasTable('asdreams_shoot_bookings')) {
                $shootUnread = AsdreamsShootBooking::shoot()->unread()->count();
                $studioUnread = AsdreamsShootBooking::studio()->unread()->count();
            } else {
                $shootUnread = 0;
                $studioUnread = 0;
            }

            $view->with('newAsdOfferEnquiriesCount', $offerUnread)
                ->with('newAsdContactEnquiriesCount', $contactUnread)
                ->with('newAsdShootBookingsCount', $shootUnread)
                ->with('newAsdStudioBookingsCount', $studioUnread);
        });
    }
}

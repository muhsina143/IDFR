<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the IDFR Admin Dashboard.
     */
    public function index(): View
    {
        // 1. Calculate Real Metric Counts directly from database tables
        $metrics = [
            'total_models' => [
                'count' => $this->safeCount('models'),
                'exists' => Schema::hasTable('models'),
                'label' => 'Total Models',
                'description' => ($this->safeCount('models', ['status' => 'published']) ?? 0).' Published on Portal',
                'icon' => 'users',
            ],
            'total_designers' => [
                'count' => $this->safeCount('designers'),
                'exists' => Schema::hasTable('designers'),
                'label' => 'Total Designers',
                'description' => ($this->safeCount('designers', ['status' => 'published']) ?? 0).' Active Couture Brands',
                'icon' => 'star',
            ],
            'total_professionals' => [
                'count' => $this->safeCount('creative_professionals'),
                'exists' => Schema::hasTable('creative_professionals'),
                'label' => 'Creative Professionals',
                'description' => ($this->safeCount('creative_professionals', ['status' => 'published']) ?? 0).' Verified Specialists',
                'icon' => 'camera',
            ],
            'total_seasons' => [
                'count' => $this->safeCount('seasons'),
                'exists' => Schema::hasTable('seasons'),
                'label' => 'Runway Seasons',
                'description' => ($this->safeCount('seasons', ['status' => 'published']) ?? 0).' Published Editions',
                'icon' => 'calendar',
            ],
            'total_gallery' => [
                'count' => $this->safeCount('gallery_images'),
                'exists' => Schema::hasTable('gallery_images'),
                'label' => 'Gallery Images',
                'description' => ($this->safeCount('gallery_images', ['status' => 'published']) ?? 0).' Runway Looks Published',
                'icon' => 'image',
            ],
            'total_enquiries' => [
                'count' => $this->safeCount('contact_enquiries'),
                'exists' => Schema::hasTable('contact_enquiries'),
                'label' => 'Contact Enquiries',
                'description' => ($this->safeCount('contact_enquiries', ['status' => 'unread']) ?? 0).' New Inbound Messages',
                'icon' => 'mail',
            ],
            'total_profile_enquiries' => [
                'count' => $this->safeCount('profile_enquiries'),
                'exists' => Schema::hasTable('profile_enquiries'),
                'label' => 'Profile Enquiries',
                'description' => ($this->safeCount('profile_enquiries', ['status' => 'new']) ?? 0).' New Booking Requests',
                'icon' => 'user-check',
            ],
            'total_partners' => [
                'count' => $this->safeCount('partners'),
                'exists' => Schema::hasTable('partners'),
                'label' => 'Our Partners',
                'description' => ($this->safeCount('partners', ['is_active' => true]) ?? 0).' Active Collaborators',
                'icon' => 'users',
            ],
            'total_sponsors' => [
                'count' => $this->safeCount('sponsors'),
                'exists' => Schema::hasTable('sponsors'),
                'label' => 'Our Sponsors',
                'description' => ($this->safeCount('sponsors', ['is_active' => true]) ?? 0).' Active Sponsors',
                'icon' => 'star',
            ],
            'total_clients' => [
                'count' => $this->safeCount('clients'),
                'exists' => Schema::hasTable('clients'),
                'label' => 'Clients & Testimonials',
                'description' => ($this->safeCount('clients', ['is_active' => true]) ?? 0).' Active Client Stories',
                'icon' => 'mail',
            ],
            'total_registrations' => [
                'count' => User::where('is_admin', false)->where('role', '!=', 'admin')->count(),
                'exists' => true,
                'label' => 'Total Registrations',
                'description' => 'Registered Portal Members',
                'icon' => 'user-check',
            ],
        ];

        // 2. Fetch Recent Admin Activities
        $activities = AdminActivity::with('user')
            ->latest()
            ->take(8)
            ->get();

        // 3. Fetch Recent Registrations
        $recentRegistrations = User::where('is_admin', false)
            ->where('role', '!=', 'admin')
            ->latest()
            ->take(5)
            ->get();

        // 4. System Diagnostic Telemetry
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => config('database.default'),
            'database_name' => config('database.connections.'.config('database.default').'.database'),
            'server_environment' => app()->environment(),
        ];

        return view('admin.dashboard', compact('metrics', 'activities', 'recentRegistrations', 'systemInfo'));
    }

    /**
     * Display a safe luxury placeholder for modules in preparation.
     */
    public function placeholder(string $module): View
    {
        $modules = [
            'seasons' => [
                'title' => 'Seasons Management',
                'desc' => 'Configure Fashion Runway seasons, schedules, venue locations, and ticket tier allocations.',
                'icon' => 'calendar',
                'badge' => 'Scheduled for Phase 2',
            ],
            'gallery' => [
                'title' => 'Gallery & Media Manager',
                'desc' => 'Manage high-resolution runway looks, backstage portfolios, and curated press photography.',
                'icon' => 'image',
                'badge' => 'Scheduled for Phase 2',
            ],
            'models' => [
                'title' => 'Model Talent Directory',
                'desc' => 'Review applicant submissions, verify portfolio measurements, and manage talent bookings.',
                'icon' => 'users',
                'badge' => 'Scheduled for Phase 2',
            ],
            'designers' => [
                'title' => 'Designers & Brands Showcase',
                'desc' => 'Manage participating couture labels, fashion designer profiles, and collection lookbooks.',
                'icon' => 'star',
                'badge' => 'Scheduled for Phase 2',
            ],
            'professionals' => [
                'title' => 'Creative Professionals Roster',
                'desc' => 'Review and manage photographers, stylists, videographers, makeup artists, and drone pilots.',
                'icon' => 'camera',
                'badge' => 'Scheduled for Phase 2',
            ],
            'enquiries' => [
                'title' => 'Contact & Sponsorship Enquiries',
                'desc' => 'Review inbound communication, sponsor leads, and audience VIP booking enquiries.',
                'icon' => 'mail',
                'badge' => 'Scheduled for Phase 2',
            ],
            'registrations' => [
                'title' => 'Member Registrations',
                'desc' => 'Oversee user accounts, talent registrations, and member security.',
                'icon' => 'user-check',
                'badge' => 'Scheduled for Phase 2',
            ],
            'settings' => [
                'title' => 'Executive Site Settings',
                'desc' => 'Brand parameters, social connections, SEO meta configurations, and notification alerts.',
                'icon' => 'settings',
                'badge' => 'Scheduled for Phase 2',
            ],
        ];

        $info = $modules[$module] ?? [
            'title' => ucfirst($module),
            'desc' => 'Administrative module configuration workspace.',
            'icon' => 'grid',
            'badge' => 'In Development',
        ];

        return view('admin.placeholder', compact('info', 'module'));
    }

    /**
     * Safely count records in a table if it exists.
     */
    private function safeCount(string $table, array $where = []): ?int
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        $query = DB::table($table);

        foreach ($where as $column => $value) {
            if (Schema::hasColumn($table, $column)) {
                $query->where($column, $value);
            }
        }

        return $query->count();
    }
}

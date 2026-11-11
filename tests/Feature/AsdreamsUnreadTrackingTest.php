<?php

namespace Tests\Feature;

use App\Models\Asdreams\AsdreamsContactEnquiry;
use App\Models\Asdreams\AsdreamsOffer;
use App\Models\Asdreams\AsdreamsOfferEnquiry;
use App\Models\Asdreams\AsdreamsShootBooking;
use App\Models\User;
use Database\Seeders\AsdreamsDataSeeder;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AsdreamsUnreadTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
        $this->seed(AsdreamsDataSeeder::class);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_unread_test@idfr.example'],
            [
                'name' => 'Unread Tracking Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    public function test_index_lists_and_filters_do_not_mark_enquiries_or_bookings_as_read(): void
    {
        $offer = AsdreamsOffer::first();

        for ($i = 1; $i <= 4; $i++) {
            AsdreamsOfferEnquiry::create([
                'offer_id' => $offer?->id,
                'offer_title' => $offer?->title ?? 'Editorial Shoot',
                'name' => "Offer Lead {$i}",
                'phone' => "+91 900000000{$i}",
                'message' => 'Interested in package.',
                'status' => 'new',
            ]);
        }

        AsdreamsContactEnquiry::create([
            'brand' => 'asdreams',
            'name' => 'Contact Lead 1',
            'phone' => '+91 9111111111',
            'subject' => 'Pricing Question',
            'message' => 'Need rates for a full day.',
            'status' => 'new',
        ]);

        for ($i = 1; $i <= 2; $i++) {
            AsdreamsShootBooking::create([
                'name' => "Shoot Client {$i}",
                'phone' => "+91 922222222{$i}",
                'booking_type' => 'shoot',
                'shoot_type' => 'Bridal Story',
                'subject' => 'Bridal Story',
                'status' => 'new',
            ]);
        }

        AsdreamsShootBooking::create([
            'name' => 'Studio Client 1',
            'phone' => '+91 9333333331',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Lookbook Shoot',
            'status' => 'new',
        ]);

        // Already-contacted records must not be included in unread badge totals.
        AsdreamsOfferEnquiry::create([
            'offer_id' => $offer?->id,
            'offer_title' => $offer?->title ?? 'Editorial Shoot',
            'name' => 'Processed Offer Lead',
            'phone' => '+91 9444444444',
            'message' => 'Already handled.',
            'status' => 'contacted',
            'read_at' => now()->subDay(),
        ]);

        $this->actingAs($this->adminUser, 'admin');

        $offerList = $this->get(route('admin.asdreams.enquiries.index', ['type' => 'offers', 'status' => 'new']));
        $offerList->assertOk();
        $this->assertSidebarBadge($offerList->getContent(), 'Enquiry Management', 5);
        $this->assertSidebarBadge($offerList->getContent(), 'Shoot Bookings', 2);
        $this->assertSidebarBadge($offerList->getContent(), 'Studio Booking Enquiries', 1);
        $offerList->assertSee('4 new');
        $offerList->assertSee('1 new');

        $contactList = $this->get(route('admin.asdreams.enquiries.index', ['type' => 'contact', 'search' => 'Pricing']));
        $contactList->assertOk();
        $this->assertSidebarBadge($contactList->getContent(), 'Enquiry Management', 5);

        $shootList = $this->get(route('admin.asdreams.shoot-bookings.index'));
        $shootList->assertOk();
        $this->assertSidebarBadge($shootList->getContent(), 'Shoot Bookings', 2);

        $studioList = $this->get(route('admin.asdreams.studio-bookings.index'));
        $studioList->assertOk();
        $this->assertSidebarBadge($studioList->getContent(), 'Studio Booking Enquiries', 1);

        $this->assertSame(4, AsdreamsOfferEnquiry::unread()->count());
        $this->assertSame(1, AsdreamsContactEnquiry::unread()->count());
        $this->assertSame(2, AsdreamsShootBooking::shoot()->unread()->count());
        $this->assertSame(1, AsdreamsShootBooking::studio()->unread()->count());
    }

    public function test_opening_show_pages_marks_unread_records_read_and_decrements_badges(): void
    {
        $offer = AsdreamsOffer::first();

        $offerEnquiry = AsdreamsOfferEnquiry::create([
            'offer_id' => $offer?->id,
            'offer_title' => $offer?->title ?? 'Editorial Shoot',
            'name' => 'Unread Offer Client',
            'phone' => '+91 9555555551',
            'message' => 'Looking for booking details.',
            'status' => 'new',
        ]);

        $contactEnquiry = AsdreamsContactEnquiry::create([
            'brand' => 'asdreams',
            'name' => 'Unread Contact Client',
            'phone' => '+91 9555555552',
            'subject' => 'General Info',
            'message' => 'Hello studio team.',
            'status' => 'new',
        ]);

        $shoot1 = AsdreamsShootBooking::create([
            'name' => 'Unread Shoot One',
            'phone' => '+91 9555555553',
            'booking_type' => 'shoot',
            'shoot_type' => 'Fashion Editorial',
            'subject' => 'Fashion Editorial',
            'status' => 'new',
        ]);

        $shoot2 = AsdreamsShootBooking::create([
            'name' => 'Unread Shoot Two',
            'phone' => '+91 9555555554',
            'booking_type' => 'shoot',
            'shoot_type' => 'Portrait Session',
            'subject' => 'Portrait Session',
            'status' => 'new',
        ]);

        $studioBooking = AsdreamsShootBooking::create([
            'name' => 'Unread Studio Client',
            'phone' => '+91 9555555555',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Ad Film Shoot',
            'status' => 'new',
        ]);

        $this->actingAs($this->adminUser, 'admin');

        // 1. Open Offer Enquiry detail -> badge drops 2 -> 1, tab shows 0 offer unread & 1 contact unread
        $showOffer = $this->get(route('admin.asdreams.enquiries.show-offer', $offerEnquiry));
        $showOffer->assertOk();
        $offerEnquiry->refresh();
        $this->assertNotNull($offerEnquiry->read_at);
        $this->assertSame('read', $offerEnquiry->status);
        $this->assertSidebarBadge($showOffer->getContent(), 'Enquiry Management', 1);

        $enquiriesAfterOffer = $this->get(route('admin.asdreams.enquiries.index', ['type' => 'offers']));
        $enquiriesAfterOffer->assertOk();
        $this->assertSidebarBadge($enquiriesAfterOffer->getContent(), 'Enquiry Management', 1);
        $enquiriesAfterOffer->assertDontSee('0 new');
        $enquiriesAfterOffer->assertSee('1 new');
        $enquiriesAfterOffer->assertSee('read');

        // 2. Open Contact Message detail -> Enquiry Management badge disappears (count 0)
        $showContact = $this->get(route('admin.asdreams.enquiries.show-contact', $contactEnquiry));
        $showContact->assertOk();
        $contactEnquiry->refresh();
        $this->assertNotNull($contactEnquiry->read_at);
        $this->assertSame('read', $contactEnquiry->status);
        $this->assertSidebarBadgeHidden($showContact->getContent(), 'Enquiry Management');

        // 3. Open first Shoot Booking -> Shoot Bookings badge drops 2 -> 1, then second -> disappears
        $showShoot1 = $this->get(route('admin.asdreams.shoot-bookings.show', $shoot1));
        $showShoot1->assertOk();
        $shoot1->refresh();
        $this->assertNotNull($shoot1->read_at);
        $this->assertSame('read', $shoot1->status);
        $this->assertSidebarBadge($showShoot1->getContent(), 'Shoot Bookings', 1);

        $showShoot2 = $this->get(route('admin.asdreams.shoot-bookings.show', $shoot2));
        $showShoot2->assertOk();
        $shoot2->refresh();
        $this->assertNotNull($shoot2->read_at);
        $this->assertSame('read', $shoot2->status);
        $this->assertSidebarBadgeHidden($showShoot2->getContent(), 'Shoot Bookings');

        // 4. Open Studio Booking detail -> Studio Booking Enquiries badge disappears
        $showStudio = $this->get(route('admin.asdreams.studio-bookings.show', $studioBooking));
        $showStudio->assertOk();
        $studioBooking->refresh();
        $this->assertNotNull($studioBooking->read_at);
        $this->assertSame('read', $studioBooking->status);
        $this->assertSidebarBadgeHidden($showStudio->getContent(), 'Studio Booking Enquiries');
    }

    public function test_opening_already_read_record_does_not_overwrite_read_at_or_status_and_manual_status_updates_work(): void
    {
        $originalReadAt = Carbon::parse('2026-11-10 09:15:00');

        $contact = AsdreamsContactEnquiry::create([
            'brand' => 'lakme',
            'name' => 'Existing Contacted Lead',
            'phone' => '+91 9666666661',
            'subject' => 'Makeup Course',
            'message' => 'Following up.',
            'status' => 'contacted',
            'read_at' => $originalReadAt,
        ]);

        $shootBooking = AsdreamsShootBooking::create([
            'name' => 'Confirmed Shoot Client',
            'phone' => '+91 9666666662',
            'booking_type' => 'shoot',
            'shoot_type' => 'Commercial',
            'status' => 'confirmed',
            'read_at' => $originalReadAt,
        ]);

        $this->actingAs($this->adminUser, 'admin');

        $contactShow = $this->get(route('admin.asdreams.enquiries.show-contact', $contact));
        $contactShow->assertOk();
        $contactShow->assertSee('<input type="hidden" name="_method" value="PUT">', false);
        $contact->refresh();
        $this->assertSame('contacted', $contact->status);
        $this->assertTrue($contact->read_at->equalTo($originalReadAt));

        // Manually changing contact status from dropdown works cleanly.
        $this->put(route('admin.asdreams.enquiries.update-contact-status', $contact), [
            'status' => 'archived',
            'admin_notes' => 'Closed after follow-up.',
        ])->assertRedirect();
        $contact->refresh();
        $this->assertSame('archived', $contact->status);
        $this->assertTrue($contact->read_at->equalTo($originalReadAt));

        $this->get(route('admin.asdreams.shoot-bookings.show', $shootBooking))->assertOk();
        $shootBooking->refresh();
        $this->assertSame('confirmed', $shootBooking->status);
        $this->assertTrue($shootBooking->read_at->equalTo($originalReadAt));

        // Manually setting status back to 'new' resets read_at and restores unread badge count.
        $this->put(route('admin.asdreams.shoot-bookings.update-status', $shootBooking), [
            'status' => 'new',
            'admin_notes' => 'Re-opened for follow-up.',
        ])->assertRedirect();

        $shootBooking->refresh();
        $this->assertSame('new', $shootBooking->status);
        $this->assertNull($shootBooking->read_at);

        $listResponse = $this->get(route('admin.asdreams.shoot-bookings.index'));
        $listResponse->assertOk();
        $this->assertSidebarBadge($listResponse->getContent(), 'Shoot Bookings', 1);

        // Updating status to 'completed' from dropdown marks it handled and clears unread badge.
        $this->put(route('admin.asdreams.shoot-bookings.update-status', $shootBooking), [
            'status' => 'completed',
            'admin_notes' => 'Closed cleanly.',
        ])->assertRedirect();

        $shootBooking->refresh();
        $this->assertSame('completed', $shootBooking->status);
        $this->assertNotNull($shootBooking->read_at);

        $afterComplete = $this->get(route('admin.asdreams.shoot-bookings.index'));
        $afterComplete->assertOk();
        $this->assertSidebarBadgeHidden($afterComplete->getContent(), 'Shoot Bookings');
    }

    protected function assertSidebarBadge(string $html, string $menuLabel, int $expectedCount): void
    {
        $sidebarHtml = $this->extractSidebarHtml($html);
        $pattern = '/<span>'.preg_quote($menuLabel, '/').'<\/span>\s*<span[^>]*>\s*'.$expectedCount.'\s*<\/span>/s';
        $this->assertMatchesRegularExpression($pattern, $sidebarHtml);
    }

    protected function assertSidebarBadgeHidden(string $html, string $menuLabel): void
    {
        $sidebarHtml = $this->extractSidebarHtml($html);
        $pattern = '/<span>'.preg_quote($menuLabel, '/').'<\/span>\s*<span[^>]*>\s*\d+\s*<\/span>/s';
        $this->assertDoesNotMatchRegularExpression($pattern, $sidebarHtml);
    }

    protected function extractSidebarHtml(string $html): string
    {
        preg_match('/<nav class="admin-sidebar__nav"[^>]*>.*?<\/nav>/s', $html, $matches);

        return $matches[0] ?? '';
    }
}

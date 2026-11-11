<?php

namespace Database\Seeders;

use App\Models\Asdreams\AsdreamsAboutContent;
use App\Models\Asdreams\AsdreamsBanner;
use App\Models\Asdreams\AsdreamsBrand;
use App\Models\Asdreams\AsdreamsContactSetting;
use App\Models\Asdreams\AsdreamsGalleryCategory;
use App\Models\Asdreams\AsdreamsOffer;
use App\Models\Asdreams\AsdreamsTeamMember;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Database\Seeder;

class AsdreamsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed 7 Offers with features from offer_details(2).html
        $offersData = [
            [
                'title' => 'Behind the Viewfinder',
                'slug' => 'viewfinder',
                'category_label' => 'Portrait Session · Kozhikode',
                'heading_main' => 'Behind the',
                'heading_highlight' => 'Viewfinder',
                'lead_text' => 'A directed portrait session for people who want photographs that feel quiet, close and true.',
                'image' => 'images/asdreams/hero-01.jpg',
                'notes' => 'Sessions are scheduled Tuesday to Sunday. A printed proof set can be added once the gallery is delivered.',
                'enquiry_placeholder' => 'Tell us who the portraits are for, and whether you prefer the studio or an outdoor location',
                'order' => 1,
                'features' => [
                    '90-minute session in the Kozhikode studio or at a nearby outdoor location',
                    'Lighting and posing directed for individuals, couples or small families',
                    'Wardrobe guidance before the shoot, plus on-set styling support',
                    '25 retouched photographs delivered in a private online gallery',
                    'One 12×18 fine-art print, ready to frame',
                ],
            ],
            [
                'title' => 'Prints & Polaroids',
                'slug' => 'polaroids',
                'category_label' => 'Studio Archive',
                'heading_main' => 'Prints &',
                'heading_highlight' => 'Polaroids',
                'lead_text' => 'A print-first sitting that leaves you with photographs you can hold, share and keep.',
                'image' => 'images/asdreams/hero-02.jpg',
                'notes' => 'Extra print sizes are quoted from the gallery after you have chosen your frames.',
                'enquiry_placeholder' => 'Tell us how many prints you have in mind, and whether this is a gift or a personal archive',
                'order' => 2,
                'features' => [
                    'Studio sitting styled for instant film and fine-art prints',
                    '15 retouched digital frames plus 8 instant prints from the session',
                    'Archival paper options: lustre, cotton rag or metallic',
                    'A 12-page album laid out by the studio',
                    'A keepsake sleeve for the instant prints',
                ],
            ],
            [
                'title' => 'The First Look',
                'slug' => 'first-look',
                'category_label' => 'Wedding Story · Kochi',
                'heading_main' => 'The First',
                'heading_highlight' => 'Look',
                'lead_text' => 'Coverage of the first look and the quiet hour around it, told the way the moment felt.',
                'image' => 'images/asdreams/hero-03.jpg',
                'notes' => 'Travel beyond Kochi is confirmed when the venue is booked.',
                'enquiry_placeholder' => 'Share your wedding date, venue and the moments you most want kept',
                'order' => 3,
                'features' => [
                    'Two photographers for the first look, portraits and family formals',
                    'Up to 4 hours of coverage in Kochi or a nearby venue',
                    'Candid and directed portraits, with a private timeline',
                    '80 retouched photographs delivered within three weeks',
                    'A highlight film of 60 to 90 seconds',
                ],
            ],
            [
                'title' => 'Studio Light',
                'slug' => 'studio-light',
                'category_label' => 'Fashion Editorial · Studio 01',
                'heading_main' => 'Studio',
                'heading_highlight' => 'Light',
                'lead_text' => 'An editorial studio session shaped by light, for campaigns, lookbooks and portraits.',
                'image' => 'images/asdreams/hero-08.jpg',
                'notes' => 'A model, stylist and set design can be arranged with the studio team.',
                'enquiry_placeholder' => 'Tell us about the looks, the brand and how you plan to use the photographs',
                'order' => 4,
                'features' => [
                    'Half-day hire of Studio 01 with softboxes and a seamless backdrop',
                    'Photographer and lighting assistant included',
                    'Up to three looks, with a shot list agreed in advance',
                    '30 retouched selects, colour-graded for print or web',
                    'Usage for a personal portfolio and one brand channel',
                ],
            ],
            [
                'title' => 'Street Frames',
                'slug' => 'street-frames',
                'category_label' => 'Documentary · Bengaluru',
                'heading_main' => 'Street',
                'heading_highlight' => 'Frames',
                'lead_text' => 'An unposed documentary walk for brands and people who want the city in the frame.',
                'image' => 'images/asdreams/hero-05.jpg',
                'notes' => 'Permits for private properties are arranged before the shoot day.',
                'enquiry_placeholder' => 'Tell us the neighbourhood, the people involved and the story you want told',
                'order' => 5,
                'features' => [
                    'Three-hour documentary shoot on location in Bengaluru',
                    'Available light, with a small kit that stays out of the way',
                    'Environmental portraits and observational frames',
                    '40 edited photographs, graded as one story',
                    'A contact sheet so you can see how the edit was made',
                ],
            ],
            [
                'title' => 'Golden Vows',
                'slug' => 'golden-vows',
                'category_label' => 'Wedding · Munnar',
                'heading_main' => 'Golden',
                'heading_highlight' => 'Vows',
                'lead_text' => 'Wedding coverage from the first ritual to the last dance, told with warmth.',
                'image' => 'images/asdreams/hero-06.jpg',
                'notes' => 'A second-day add-on is available for welcome events and brunches.',
                'enquiry_placeholder' => 'Share your wedding date, rituals and where the day will be celebrated',
                'order' => 6,
                'features' => [
                    'Full-day photo and film team for a wedding in Munnar',
                    'Ceremony, portraits and reception, planned around your rituals',
                    '400 retouched photographs and a film of 3 to 5 minutes',
                    'Online gallery with download and print release for the couple',
                    'A consultation to lock the timeline before the day',
                ],
            ],
            [
                'title' => 'Above the Clouds',
                'slug' => 'above-clouds',
                'category_label' => 'Destination · Wayanad',
                'heading_main' => 'Above the',
                'heading_highlight' => 'Clouds',
                'lead_text' => 'A destination shoot that travels with you, for portraits above the hills.',
                'image' => 'images/asdreams/hero-07.jpg',
                'notes' => 'One weather postponement is included, so the light can be right.',
                'enquiry_placeholder' => 'Tell us your travel dates, where you are staying and whether you prefer sunrise or sunset',
                'order' => 7,
                'features' => [
                    'Sunrise or sunset session at a Wayanad viewpoint',
                    'Photographer, assistant and a compact lighting kit',
                    'Up to 3 hours on location, including travel from your stay',
                    '35 retouched photographs in a private gallery',
                    'A short vertical film for sharing the day',
                ],
            ],
        ];

        $offerMap = [];
        foreach ($offersData as $data) {
            $features = $data['features'];
            unset($data['features']);

            $offer = AsdreamsOffer::updateOrCreate(['slug' => $data['slug']], $data);
            $offerMap[$offer->slug] = $offer;

            $offer->features()->delete();
            foreach ($features as $idx => $feat) {
                $offer->features()->create([
                    'feature_text' => $feat,
                    'order' => $idx + 1,
                ]);
            }
        }

        // 2. Seed 7 Hero Banners linked to offers
        $bannersData = [
            [
                'title' => 'Behind the Viewfinder',
                'highlight_title' => 'Viewfinder',
                'label' => 'Portrait Session · Kozhikode',
                'description' => 'Stories, emotions and timeless moments captured through our lens.',
                'image' => 'images/asdreams/hr2.png',
                'alt_text' => 'Photographer framing a shot through her camera',
                'button_text' => 'Offer Details',
                'linked_offer_slug' => 'viewfinder',
                'order' => 1,
            ],
            [
                'title' => 'Prints & Polaroids',
                'highlight_title' => 'Polaroids',
                'label' => 'Studio Archive',
                'description' => 'Every frame printed with care: keepsakes to hold, share and treasure.',
                'image' => 'images/asdreams/hr3.png',
                'alt_text' => 'Vintage camera resting beside instant photo prints',
                'button_text' => 'Offer Details',
                'linked_offer_slug' => 'polaroids',
                'order' => 2,
            ],
            [
                'title' => 'The First Look',
                'highlight_title' => 'Look',
                'label' => 'Wedding Story · Kochi',
                'description' => 'Quiet glances and joyful tears, documented the way you felt them.',
                'image' => 'images/asdreams/hr4.png',
                'alt_text' => 'Bride and groom sharing a quiet moment',
                'button_text' => 'Offer Details',
                'linked_offer_slug' => 'first-look',
                'order' => 3,
            ],
            [
                'title' => 'Studio Light',
                'highlight_title' => 'Light',
                'label' => 'Fashion Editorial · Studio 01',
                'description' => 'Editorial portraits and fashion campaigns shaped by light in our studio.',
                'image' => 'images/asdreams/hero-08.jpg',
                'alt_text' => 'Photography studio set with softboxes and a white backdrop',
                'button_text' => 'Offer Details',
                'linked_offer_slug' => 'studio-light',
                'order' => 4,
            ],
            [
                'title' => 'Street Frames',
                'highlight_title' => 'Frames',
                'label' => 'Documentary · Bengaluru',
                'description' => 'An unposed documentary walk for brands and people who want the city in the frame.',
                'image' => 'images/asdreams/hero-05.jpg',
                'alt_text' => 'Photographer shooting on a busy city street',
                'button_text' => 'Offer Details',
                'linked_offer_slug' => 'street-frames',
                'order' => 5,
            ],
            [
                'title' => 'Golden Vows',
                'highlight_title' => 'Vows',
                'label' => 'Wedding · Munnar',
                'description' => 'Wedding coverage from the first ritual to the last dance, told with warmth.',
                'image' => 'images/asdreams/hero-06.jpg',
                'alt_text' => 'Wedding couple embracing outdoors',
                'button_text' => 'Offer Details',
                'linked_offer_slug' => 'golden-vows',
                'order' => 6,
            ],
            [
                'title' => 'Above the Clouds',
                'highlight_title' => 'Clouds',
                'label' => 'Destination · Wayanad',
                'description' => 'A destination shoot that travels with you, for portraits above the hills.',
                'image' => 'images/asdreams/hero-07.jpg',
                'alt_text' => 'Photographer on a mountain peak above a sea of clouds',
                'button_text' => 'Offer Details',
                'linked_offer_slug' => 'above-clouds',
                'order' => 7,
            ],
        ];

        foreach ($bannersData as $bData) {
            $slug = $bData['linked_offer_slug'];
            $linkedOfferId = isset($offerMap[$slug]) ? $offerMap[$slug]->id : null;
            $bData['linked_offer_id'] = $linkedOfferId;

            AsdreamsBanner::updateOrCreate(['order' => $bData['order']], $bData);
        }

        // 3. Seed 4 Brands
        $brandsData = [
            [
                'name' => 'ASDreamStudio',
                'tagline' => 'Photography & Videography',
                'description' => 'Photography, runway, model training and beauty education — four houses working together to launch careers in fashion.',
                'logo_path' => 'images/asdreams/asd-logo.png',
                'cover_image' => 'images/asdreams/brands/asdreamstudio.jpg',
                'link_url' => '/asdreams',
                'tags' => ['Fashion', 'Wedding', 'Events', 'Commercial', 'Products', 'Studio Rental'],
                'css_variant' => 'standard',
                'order' => 1,
            ],
            [
                'name' => 'IDFR',
                'tagline' => 'Indian Designer Fashion Runway',
                'description' => 'Premier Indian fashion runway and talent showcase.',
                'logo_path' => 'images/logo/idfr-logo.png',
                'cover_image' => 'images/hero/hero-04.jpg',
                'link_url' => '/idfr',
                'tags' => ['Designers', 'Models', 'Fashion Shows', 'Brands', 'Events', 'Networking'],
                'css_variant' => 'wide',
                'order' => 2,
            ],
            [
                'name' => "Athul's Academy",
                'tagline' => 'Modelling & Grooming',
                'description' => 'A finishing school for models — from first walk to first casting.',
                'logo_path' => 'images/asdreams/brands/athuls-academy-logo.png',
                'cover_image' => 'images/models/model-01.jpg',
                'link_url' => '/asdreams/contact?brand=athuls',
                'tags' => ['Model Training', 'Grooming', 'Portfolio', 'Casting', 'Industry Opportunities'],
                'css_variant' => 'tall',
                'order' => 3,
            ],
            [
                'name' => 'Lakme',
                'tagline' => 'Makeup · Hair | Nails · Skin',
                'description' => 'Industry-led beauty and styling courses for aspiring makeup artists, hair stylists and fashion stylists — with live backstage experience at IDFR fashion shows.',
                'logo_path' => 'images/asdreams/brands/lak1_new.png',
                'cover_image' => 'images/asdreams/brands/lakme-academy.jpg',
                'link_url' => '/asdreams/contact?brand=lakme',
                'tags' => ['Professional Courses', 'Workshops', 'Practical Training', 'Certification', 'Creative Development'],
                'css_variant' => 'lakme',
                'order' => 4,
            ],
        ];

        foreach ($brandsData as $brData) {
            AsdreamsBrand::updateOrCreate(['order' => $brData['order']], $brData);
        }

        // 4. Seed About Content
        AsdreamsAboutContent::current();

        // 5. Seed Gallery Categories & Images
        $categoriesData = [
            [
                'name' => 'Weddings',
                'slug' => 'weddings',
                'tab_label' => 'Wedding',
                'order' => 1,
                'images' => [
                    ['title' => 'Vows at Golden Hour', 'path' => 'images/asdreams/gallery/wedding-01.jpg', 'w' => 1000, 'h' => 1500],
                    ['title' => 'The Bridal Walk', 'path' => 'images/asdreams/gallery/wedding-02.jpg', 'w' => 1000, 'h' => 668],
                    ['title' => 'Under the Palms', 'path' => 'images/asdreams/gallery/wedding-03.jpg', 'w' => 1000, 'h' => 1500],
                    ['title' => 'Family Blessings', 'path' => 'images/asdreams/gallery/wedding-04.jpg', 'w' => 1000, 'h' => 1500],
                    ['title' => 'Bouquet in Candlelight', 'path' => 'images/asdreams/gallery/wedding-05.jpg', 'w' => 1000, 'h' => 667],
                    ['title' => 'A Quiet Glance', 'path' => 'images/asdreams/gallery/wedding-06.jpg', 'w' => 1000, 'h' => 1500],
                ],
            ],
            [
                'name' => 'Fashion Runway',
                'slug' => 'fashion',
                'tab_label' => 'Fashion',
                'order' => 2,
                'images' => [
                    ['title' => 'Couture Silhouette', 'path' => 'images/asdreams/gallery/fashion-01.jpg', 'w' => 1000, 'h' => 1500],
                    ['title' => 'Backstage Motion', 'path' => 'images/asdreams/gallery/fashion-02.jpg', 'w' => 1000, 'h' => 667],
                    ['title' => 'Season 4 Finale', 'path' => 'images/asdreams/gallery/fashion-03.jpg', 'w' => 1000, 'h' => 1500],
                    ['title' => 'Silk in Flight', 'path' => 'images/asdreams/gallery/fashion-04.jpg', 'w' => 1000, 'h' => 1500],
                ],
            ],
            [
                'name' => 'Portraits',
                'slug' => 'portraits',
                'tab_label' => 'Portraits',
                'order' => 3,
                'images' => [
                    ['title' => 'The Artist', 'path' => 'images/asdreams/gallery/portrait-01.jpg', 'w' => 1000, 'h' => 1500],
                    ['title' => 'Window Light', 'path' => 'images/asdreams/gallery/portrait-02.jpg', 'w' => 1000, 'h' => 667],
                    ['title' => 'Shadow & Line', 'path' => 'images/asdreams/gallery/portrait-03.jpg', 'w' => 1000, 'h' => 1500],
                ],
            ],
            [
                'name' => 'Films & Motion',
                'slug' => 'films',
                'tab_label' => 'Films',
                'order' => 4,
                'images' => [
                    ['title' => 'Behind the Anamorphic Lens', 'path' => 'images/asdreams/gallery/film-01.jpg', 'w' => 1000, 'h' => 667],
                    ['title' => 'Munnar Tea Trails', 'path' => 'images/asdreams/gallery/film-02.jpg', 'w' => 1000, 'h' => 1500],
                ],
            ],
            [
                'name' => 'Events',
                'slug' => 'events',
                'tab_label' => 'Events',
                'order' => 5,
                'images' => [
                    ['title' => 'Awards Night Gala', 'path' => 'images/asdreams/gallery/event-01.jpg', 'w' => 1000, 'h' => 667],
                    ['title' => 'The Grand Hall', 'path' => 'images/asdreams/gallery/event-02.jpg', 'w' => 1000, 'h' => 1500],
                ],
            ],
        ];

        foreach ($categoriesData as $cData) {
            $images = $cData['images'];
            unset($cData['images']);

            $cat = AsdreamsGalleryCategory::updateOrCreate(['slug' => $cData['slug']], $cData);
            $cat->images()->delete();

            foreach ($images as $iIdx => $img) {
                $cat->images()->create([
                    'title' => $img['title'],
                    'image_path' => $img['path'],
                    'width' => $img['w'],
                    'height' => $img['h'],
                    'order' => $iIdx + 1,
                    'is_published' => true,
                ]);
            }
        }

        // 6. Seed Team Members
        $teamData = [
            ['name' => 'Meera Nair', 'designation' => 'Creative Director', 'photo_path' => 'images/founders/tt6.jpg', 'order' => 1],
            ['name' => 'Arjun Menon', 'designation' => 'Lead Photographer', 'photo_path' => 'images/founders/tt1.webp', 'order' => 2],
            ['name' => 'Sara Iqbal', 'designation' => 'Cinematographer', 'photo_path' => 'images/founders/tt3.webp', 'order' => 3],
            ['name' => 'Rohan Das', 'designation' => 'Photo Editor', 'photo_path' => 'images/founders/tt5.webp', 'order' => 4],
            ['name' => 'Diya Krishnan', 'designation' => 'Fashion Stylist', 'photo_path' => 'images/founders/tt6.jpg', 'order' => 5],
            ['name' => 'Kabir Shah', 'designation' => 'Drone Pilot', 'photo_path' => 'images/founders/tt6.jpg', 'order' => 6],
            ['name' => 'Nila Thomas', 'designation' => 'Makeup Artist', 'photo_path' => 'images/founders/tt6.jpg', 'order' => 7],
        ];

        foreach ($teamData as $tData) {
            AsdreamsTeamMember::updateOrCreate(['name' => $tData['name']], $tData);
        }

        // 7. Seed Contact Settings
        AsdreamsContactSetting::current();

        // 8. Seed Website Settings
        AsdreamsWebsiteSetting::set('hero_overlay_top_word', 'Photography');
        AsdreamsWebsiteSetting::set('hero_overlay_bottom_word', 'Capturing');
        AsdreamsWebsiteSetting::set('hero_overlay_bottom_highlight', 'Moments');

        // 9. Seed Our Clients & Moving Client Logos independently
        $this->call(AsdreamsClientsAndLogosSeeder::class);

        // 10. Seed Studio Rental Management (Facilities, Equipment, Studio Gallery)
        $this->call(AsdreamsStudioRentalSeeder::class);
    }
}

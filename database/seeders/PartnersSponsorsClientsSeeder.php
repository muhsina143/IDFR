<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\MovingLogo;
use App\Models\Partner;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class PartnersSponsorsClientsSeeder extends Seeder
{
    /**
     * Seed partners, sponsors, clients, and moving logo strip items idempotently.
     */
    public function run(): void
    {
        // 1. Partners
        $partners = [
            [
                'name' => 'Lumière Studios',
                'type' => 'Photography Partner',
                'description' => 'Official portfolio and campaign photography for IDFR models.',
                'image_path' => 'images/partners/partner-01.jpg',
                'logo_style' => 'circle',
                'logo_mark' => 'L',
                'logo_word' => 'LUMIÈRE',
                'logo_sub' => 'STUDIOS',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Atelier Noor',
                'type' => 'Couture Partner',
                'description' => 'Signature couture showcased across every IDFR finale.',
                'image_path' => 'images/partners/partner-02.jpg',
                'logo_style' => 'diamond',
                'logo_mark' => 'N',
                'logo_word' => 'NOOR',
                'logo_sub' => 'ATELIER',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Kaya Textiles',
                'type' => 'Textile Partner',
                'description' => 'Handwoven Kerala fabrics for our designer collections.',
                'image_path' => 'images/partners/partner-03.jpg',
                'logo_style' => 'crest',
                'logo_mark' => 'K',
                'logo_word' => 'KAYA',
                'logo_sub' => 'TEXTILES',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Grace Grooming Lounge',
                'type' => 'Grooming Partner',
                'description' => 'Hair, skin and grooming for models on show days.',
                'image_path' => 'images/partners/partner-04.jpg',
                'logo_style' => 'rings',
                'logo_mark' => 'G',
                'logo_word' => 'GRACE',
                'logo_sub' => 'GROOMING LOUNGE',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Stagecraft Events',
                'type' => 'Production Partner',
                'description' => 'Runway design, lighting and live show production.',
                'image_path' => 'images/partners/partner-05.jpg',
                'logo_style' => 'bars',
                'logo_mark' => 'S',
                'logo_word' => 'STAGECRAFT',
                'logo_sub' => 'EVENTS',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Velvet Beauty Co.',
                'type' => 'Makeup Partner',
                'description' => 'Backstage makeup artistry for every runway look.',
                'image_path' => 'images/partners/partner-06.jpg',
                'logo_style' => 'star',
                'logo_mark' => 'V',
                'logo_word' => 'VELVET',
                'logo_sub' => 'BEAUTY CO.',
                'display_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($partners as $pData) {
            Partner::updateOrCreate(
                ['name' => $pData['name']],
                $pData
            );
        }

        // 2. Sponsors
        $sponsors = [
            [
                'name' => 'Aurum Jewels',
                'tier' => 'Title Sponsor · Season 4',
                'is_title' => true,
                'description' => 'Premier luxury jewellery partner presenting the IDFR Season 4 grand showcase.',
                'logo_style' => 'crown',
                'logo_mark' => 'A',
                'logo_word' => 'AURUM',
                'logo_sub' => 'FINE JEWELS',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Regal Motors',
                'tier' => 'Automobile Partner',
                'is_title' => false,
                'description' => 'Official automotive partner providing VIP transport and luxury fleet services.',
                'logo_style' => 'arch',
                'logo_mark' => 'R',
                'logo_word' => 'REGAL',
                'logo_sub' => 'MOTORS',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Ivory Perfumes',
                'tier' => 'Fragrance Partner',
                'is_title' => false,
                'description' => 'Artisanal fragrances crafted for runway models and fashion events.',
                'logo_style' => 'drop',
                'logo_mark' => 'I',
                'logo_word' => 'IVORY',
                'logo_sub' => 'PARFUMS',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Kerala Weaves',
                'tier' => 'Handloom Partner',
                'is_title' => false,
                'description' => 'Reviving heritage South Indian weaving traditions on contemporary runways.',
                'logo_style' => 'weave',
                'logo_mark' => 'K',
                'logo_word' => 'KERALA',
                'logo_sub' => 'WEAVES',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Zenith Footwear',
                'tier' => 'Footwear Partner',
                'is_title' => false,
                'description' => 'High-fashion footwear engineered for runway comfort and elegance.',
                'logo_style' => 'hex',
                'logo_mark' => 'Z',
                'logo_word' => 'ZENITH',
                'logo_sub' => 'FOOTWEAR',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Opal Eyewear',
                'tier' => 'Eyewear Partner',
                'is_title' => false,
                'description' => 'Statement eyewear and designer sunglasses featured across runway collections.',
                'logo_style' => 'rings',
                'logo_mark' => 'O',
                'logo_word' => 'OPAL',
                'logo_sub' => 'EYEWEAR',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Marina Hotels',
                'tier' => 'Hospitality Partner',
                'is_title' => false,
                'description' => 'Luxury accommodation and gala hospitality partner for IDFR delegates.',
                'logo_style' => 'wave',
                'logo_mark' => 'M',
                'logo_word' => 'MARINA',
                'logo_sub' => 'HOTELS & RESORTS',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Pulse 94.3 FM',
                'tier' => 'Radio Partner',
                'is_title' => false,
                'description' => 'Exclusive broadcast and media partner bringing runway coverage across South India.',
                'logo_style' => 'bars',
                'logo_mark' => 'P',
                'logo_word' => 'PULSE',
                'logo_sub' => '94.3 FM',
                'display_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Monsoon Water',
                'tier' => 'Beverage Partner',
                'is_title' => false,
                'description' => 'Official hydration and natural spring water partner.',
                'logo_style' => 'drop',
                'logo_mark' => 'M',
                'logo_word' => 'MONSOON',
                'logo_sub' => 'SPRING WATER',
                'display_order' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($sponsors as $sData) {
            Sponsor::updateOrCreate(
                ['name' => $sData['name']],
                $sData
            );
        }

        // 3. Clients & Testimonials
        $clients = [
            [
                'name' => 'Maison Kerala',
                'industry' => 'Couture House',
                'image_path' => 'images/clients/client-featured.jpg',
                'logo_style' => 'arch',
                'logo_mark' => 'M',
                'logo_word' => 'MAISON',
                'logo_sub' => 'KERALA',
                'testimonial_quote' => 'IDFR helped us cast our entire festive campaign in a single week. The talent was professional, prepared and camera-ready.',
                'author_name' => 'Neha Varghese',
                'author_role' => 'Creative Director',
                'company_name' => 'Maison Kerala',
                'is_featured' => true,
                'include_in_strip' => true,
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Vasa Bridal',
                'industry' => 'Bridal Wear',
                'image_path' => 'images/clients/client-01.jpg',
                'logo_style' => 'diamond',
                'logo_mark' => 'V',
                'logo_word' => 'VASA',
                'logo_sub' => 'BRIDAL',
                'testimonial_quote' => 'Every bride in our lookbook was an IDFR model. Their portfolios made shortlisting effortless.',
                'author_name' => 'Lakshmi Prasad',
                'author_role' => 'Founder',
                'company_name' => 'Vasa Bridal',
                'is_featured' => false,
                'include_in_strip' => true,
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Urban Thread',
                'industry' => 'Streetwear',
                'image_path' => 'images/clients/client-02.jpg',
                'logo_style' => 'bars',
                'logo_mark' => 'U',
                'logo_word' => 'URBAN',
                'logo_sub' => 'THREAD',
                'testimonial_quote' => 'We found fresh, authentic faces for our youth line — exactly the energy our brand needed.',
                'author_name' => 'Karan Shah',
                'author_role' => 'Brand Manager',
                'company_name' => 'Urban Thread',
                'is_featured' => false,
                'include_in_strip' => true,
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Lumen Magazine',
                'industry' => 'Fashion Publication',
                'image_path' => 'images/gallery/s2-06.jpg',
                'logo_style' => 'circle',
                'logo_mark' => 'L',
                'logo_word' => 'LUMEN',
                'logo_sub' => 'MAGAZINE',
                'testimonial_quote' => 'Our cover stars from IDFR consistently deliver striking editorial work. It is our first stop for new faces.',
                'author_name' => 'Rhea Kapoor',
                'author_role' => 'Fashion Editor',
                'company_name' => 'Lumen Magazine',
                'is_featured' => false,
                'include_in_strip' => true,
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Prism Advertising',
                'industry' => 'Advertising Agency',
                'image_path' => 'images/gallery/s3-04.jpg',
                'logo_style' => 'hex',
                'logo_mark' => 'P',
                'logo_word' => 'PRISM',
                'logo_sub' => 'ADVERTISING',
                'testimonial_quote' => 'From TV commercials to digital films, IDFR talent has elevated campaigns for our biggest accounts.',
                'author_name' => 'Siddharth Rao',
                'author_role' => 'Casting Head',
                'company_name' => 'Prism Advertising',
                'is_featured' => false,
                'include_in_strip' => true,
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Kanji Silks',
                'industry' => 'Heritage Textiles',
                'image_path' => 'images/partners/partner-03.jpg',
                'logo_style' => 'weave',
                'logo_mark' => 'K',
                'logo_word' => 'KANJI',
                'logo_sub' => 'SILKS',
                'testimonial_quote' => 'IDFR models carry our heritage silks with grace. Their runway training shows in every frame.',
                'author_name' => 'Meenakshi Sundaram',
                'author_role' => 'Managing Director',
                'company_name' => 'Kanji Silks',
                'is_featured' => false,
                'include_in_strip' => true,
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Northstar Footwear',
                'industry' => 'Footwear',
                'image_path' => 'images/gallery/s2-01.jpg',
                'logo_style' => 'star',
                'logo_mark' => 'N',
                'logo_word' => 'NORTHSTAR',
                'logo_sub' => 'FOOTWEAR',
                'testimonial_quote' => 'Booking models through IDFR is fast and transparent. We now cast every season with them.',
                'author_name' => 'Imran Qureshi',
                'author_role' => 'Marketing Lead',
                'company_name' => 'Northstar Footwear',
                'is_featured' => false,
                'include_in_strip' => true,
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Aura Cosmetics',
                'industry' => 'Beauty',
                'image_path' => 'images/gallery/s2-03.jpg',
                'logo_style' => 'drop',
                'logo_mark' => 'A',
                'logo_word' => 'AURA',
                'logo_sub' => 'COSMETICS',
                'testimonial_quote' => 'The beauty talent on IDFR is outstanding — flawless skin, great expressions and total professionalism.',
                'author_name' => 'Fatima Sheikh',
                'author_role' => 'Head of Brand',
                'company_name' => 'Aura Cosmetics',
                'is_featured' => false,
                'include_in_strip' => true,
                'display_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($clients as $cData) {
            Client::updateOrCreate(
                ['name' => $cData['name']],
                $cData
            );
        }

        // 4. Moving Client Logos (Strip)
        // By default, the strip presents all active clients + active non-title sponsors
        $order = 1;
        $dbClients = Client::orderBy('display_order')->get();
        foreach ($dbClients as $cl) {
            MovingLogo::updateOrCreate(
                ['client_id' => $cl->id],
                [
                    'company_name' => $cl->name,
                    'logo_path' => $cl->logo_path,
                    'logo_style' => $cl->logo_style,
                    'logo_mark' => $cl->logo_mark,
                    'logo_word' => $cl->logo_word,
                    'logo_sub' => $cl->logo_sub,
                    'display_order' => $order++,
                    'is_active' => true,
                ]
            );
        }

        $nonTitleSponsors = Sponsor::where('is_title', false)->orderBy('display_order')->get();
        foreach ($nonTitleSponsors as $sp) {
            MovingLogo::updateOrCreate(
                ['sponsor_id' => $sp->id],
                [
                    'company_name' => $sp->name,
                    'logo_path' => $sp->logo_path,
                    'logo_style' => $sp->logo_style,
                    'logo_mark' => $sp->logo_mark,
                    'logo_word' => $sp->logo_word,
                    'logo_sub' => $sp->logo_sub,
                    'display_order' => $order++,
                    'is_active' => true,
                ]
            );
        }
    }
}

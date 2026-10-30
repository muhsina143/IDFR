<?php

namespace Database\Seeders;

use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\ModelProfile;
use App\Models\Season;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Phase3DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Runway Seasons
        $seasonsData = [
            [
                'number' => 1,
                'title' => 'Season 1',
                'year' => '2023',
                'location' => 'Kochi, Kerala',
                'dates' => '15 — 17 December 2023',
                'status' => 'published',
                'description' => 'The inaugural season of the Indian Designer Fashion Runway, uniting south Indian handloom and emerging design voices.',
            ],
            [
                'number' => 2,
                'title' => 'Season 2',
                'year' => '2024',
                'location' => 'Kozhikode, Kerala',
                'dates' => '12 — 14 December 2024',
                'status' => 'published',
                'description' => 'Season 2 expanded the runway showcase with national couture designers and international modeling scouts.',
            ],
            [
                'number' => 3,
                'title' => 'Season 3',
                'year' => '2025',
                'location' => 'Kochi, Kerala',
                'dates' => '19 — 21 December 2025',
                'status' => 'published',
                'description' => 'Season 3 celebrated luxury bridal couture and ethical sustainable fashion with record audience attendance.',
            ],
            [
                'number' => 4,
                'title' => 'Season 4',
                'year' => '2026',
                'location' => 'Kozhikode, Kerala',
                'dates' => '18 — 20 December 2026',
                'status' => 'published',
                'description' => 'Season 4 brings together emerging talent, established models, designers and fashion enthusiasts on one remarkable stage.',
            ],
        ];

        $seasonModels = [];
        foreach ($seasonsData as $sData) {
            $season = Season::updateOrCreate(['number' => $sData['number']], $sData);
            $seasonModels[$sData['number']] = $season;
        }

        // 2. Seed Models from JSON
        $modelsFile = database_path('seeders/models_data.json');
        if (file_exists($modelsFile)) {
            $models = json_decode(file_get_contents($modelsFile), true);
            $order = 1;
            foreach ($models as $mData) {
                $slug = Str::slug($mData['name']);
                if (ModelProfile::where('slug', $slug)->exists()) {
                    $slug .= '-'.$order;
                }

                $model = ModelProfile::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $mData['name'],
                        'gender' => $mData['gender'],
                        'level' => $mData['level'],
                        'is_kid' => $mData['is_kid'],
                        'age' => $mData['age'],
                        'height' => $mData['height'],
                        'location' => $mData['location'],
                        'experience' => $mData['experience'],
                        'profile_photo' => $mData['profile_photo'],
                        'types' => $mData['types'],
                        'portfolio_images' => $mData['portfolio_images'],
                        'measurements' => $mData['measurements'],
                        'skills' => $mData['skills'],
                        'specialities' => $mData['specialities'],
                        'availability' => $mData['availability'],
                        'description' => $mData['description'],
                        'is_featured' => $mData['is_featured'],
                        'display_order' => $order++,
                        'status' => 'published',
                    ]
                );

                // Associate with seasons (Season 4 for active models, Season 3 for experienced)
                $seasonsToSync = [$seasonModels[4]->id];
                if ($mData['level'] === 'Experienced') {
                    $seasonsToSync[] = $seasonModels[3]->id;
                }
                $model->seasons()->sync($seasonsToSync);
            }
        }

        // 3. Seed Creative Professionals from JSON
        $prosFile = database_path('seeders/pros_data.json');
        if (file_exists($prosFile)) {
            $pros = json_decode(file_get_contents($prosFile), true);
            $order = 1;
            foreach ($pros as $pData) {
                $slug = ! empty($pData['id_code']) ? $pData['id_code'] : Str::slug($pData['category'].'-'.$pData['name']);

                $title = ucwords(str_replace('-', ' ', $pData['category']));

                $pro = CreativeProfessional::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $pData['name'],
                        'category' => $pData['category'],
                        'professional_title' => $title,
                        'profile_photo' => $pData['profile_photo'],
                        'location' => $pData['location'],
                        'experience' => $pData['experience'],
                        'biography' => $pData['biography'],
                        'services' => $pData['services'],
                        'portfolio_images' => $pData['portfolio_images'],
                        'is_featured' => ($order <= 8),
                        'display_order' => $order++,
                        'status' => 'published',
                    ]
                );

                // Associate with Season 4 and Season 3
                $pro->seasons()->sync([$seasonModels[4]->id, $seasonModels[3]->id]);
            }
        }

        // 4. Seed Designers from fashion designers and featured collections
        $designersData = [
            [
                'name' => 'Tara Nair',
                'brand_name' => 'Tara Nair Couture',
                'slug' => 'tara-nair',
                'profile_photo' => 'images/creative/fashion-designer/tara-nair.jpg',
                'brand_logo' => 'images/logo/idfr-mark.png',
                'location' => 'Kochi, Kerala',
                'experience' => '6 years',
                'short_intro' => 'Contemporary womenswear rooted in Kerala handloom and sustainable silhouettes.',
                'biography' => 'Tara designs contemporary womenswear rooted in Kerala handloom, and has shown three collections on the IDFR runway. Her garments balance modern sculptural cuts with indigenous weaving traditions.',
                'collection_title' => 'Heritage Weft 2026',
                'collection_description' => 'A celebration of fine Kasavu handloom reimagined for contemporary eveningwear.',
                'services' => ['Bridal and occasion wear', 'Custom couture', 'Handloom collections', 'Styling consultations', 'Runway collections'],
                'portfolio_images' => [
                    ['image' => 'images/creative/portfolio/design-sketches.jpg', 'caption' => 'Collection sketches'],
                    ['image' => 'images/gallery/s3-01.jpg', 'caption' => 'Summer collection'],
                    ['image' => 'images/gallery/s1-05.jpg', 'caption' => 'Handloom capsule'],
                ],
                'website_url' => 'https://idfr.example/designers/tara-nair',
                'instagram_url' => 'https://instagram.com/taranaircouture',
                'is_featured' => true,
                'display_order' => 1,
                'status' => 'published',
                'seasons' => [3, 4],
            ],
            [
                'name' => 'Isha Menon',
                'brand_name' => 'Isha Menon Atelier',
                'slug' => 'isha-menon',
                'profile_photo' => 'images/creative/fashion-designer/isha-menon.jpg',
                'brand_logo' => 'images/logo/idfr-mark.png',
                'location' => 'Kozhikode, Kerala',
                'experience' => '8 years',
                'short_intro' => 'Modern fusion couture bringing architectural tailoring to traditional ceremonial wear.',
                'biography' => 'Isha Menon Atelier is renowned for its precision tailoring, fluid silk drapes, and innovative surface ornamentation. A regular fixture on the IDFR runway since Season 2.',
                'collection_title' => 'Nocturne Silk',
                'collection_description' => 'Hand-dyed Mulberry silks paired with structured boleros and metallic threadwork.',
                'services' => ['Occasion wear', 'Runway collections', 'Celebrity styling', 'Pre-wedding couture'],
                'portfolio_images' => [
                    ['image' => 'images/hero/hero-01.jpg', 'caption' => 'Opening runway look'],
                    ['image' => 'images/gallery/s2-04.jpg', 'caption' => 'Evening gown collection'],
                ],
                'website_url' => 'https://idfr.example/designers/isha-menon',
                'instagram_url' => 'https://instagram.com/ishamenonatelier',
                'is_featured' => true,
                'display_order' => 2,
                'status' => 'published',
                'seasons' => [2, 3, 4],
            ],
            [
                'name' => 'Diya Varma',
                'brand_name' => 'DV Studio',
                'slug' => 'diya-varma',
                'profile_photo' => 'images/creative/fashion-designer/diya-varma.jpg',
                'brand_logo' => 'images/logo/idfr-mark.png',
                'location' => 'Bengaluru, Karnataka',
                'experience' => '5 years',
                'short_intro' => 'Avant-garde streetwear meets bespoke Indian textile embroidery.',
                'biography' => 'Diya Varma brings a youth-centric, vibrant design language that resonates with new-gen runway audiences. Her pieces combine upcycled denim and traditional Zari work.',
                'collection_title' => 'Urban Zari',
                'collection_description' => 'Denim jackets, utilitarian jumpsuits, and structured kurtas laced with antique Zari borders.',
                'services' => ['Streetwear couture', 'Unisex fashion', 'Custom embroidery', 'Styling'],
                'portfolio_images' => [
                    ['image' => 'images/gallery/s3-03.jpg', 'caption' => 'Runway finale ensemble'],
                    ['image' => 'images/gallery/s2-02.jpg', 'caption' => 'Backstage look'],
                ],
                'website_url' => 'https://idfr.example/designers/diya-varma',
                'instagram_url' => 'https://instagram.com/dvstudio',
                'is_featured' => false,
                'display_order' => 3,
                'status' => 'published',
                'seasons' => [3, 4],
            ],
            [
                'name' => 'Aarav Khanna',
                'brand_name' => 'Khanna Menswear',
                'slug' => 'aarav-khanna',
                'profile_photo' => 'images/hero/hero-02.jpg',
                'brand_logo' => 'images/logo/idfr-mark.png',
                'location' => 'Mumbai, Maharashtra',
                'experience' => '9 years',
                'short_intro' => 'Pioneering contemporary Indian menswear with razor-sharp bandhgala jackets.',
                'biography' => 'Aarav Khanna is a leading menswear designer who opened the IDFR Season 4 showcase with his critically acclaimed "Neon Noir" collection.',
                'collection_title' => 'Neon Noir',
                'collection_description' => 'Monochromatic bandhgalas, silk kurtas, and velvet jackets illuminated with subtle neon piping.',
                'services' => ['Menswear couture', 'Tuxedos & Bandhgalas', 'Runway collections', 'VIP Wardrobe Consulting'],
                'portfolio_images' => [
                    ['image' => 'images/hero/hero-02.jpg', 'caption' => 'Neon Noir Look 01'],
                    ['image' => 'images/gallery/s3-05.jpg', 'caption' => 'Bandhgala suit showcase'],
                ],
                'website_url' => 'https://idfr.example/designers/aarav-khanna',
                'instagram_url' => 'https://instagram.com/aaravkhanna',
                'is_featured' => true,
                'display_order' => 4,
                'status' => 'published',
                'seasons' => [4],
            ],
            [
                'name' => 'Anjali Thomas',
                'brand_name' => 'Anjali Thomas Atelier',
                'slug' => 'anjali-thomas',
                'profile_photo' => 'images/creative/fashion-designer/anjali-thomas.jpg',
                'brand_logo' => 'images/logo/idfr-mark.png',
                'location' => 'Kochi, Kerala',
                'experience' => '7 years',
                'short_intro' => 'Romantic bridal silhouettes featuring French lace and hand-embroidered organza.',
                'biography' => 'Anjali Thomas creates couture gowns and reception ensembles that have dressed brides and red carpet celebrities across the Middle East and India.',
                'collection_title' => 'Fleur de Lis',
                'collection_description' => 'Pastel wedding gowns with illusion necklines and cascading hand-embroidered floral motifs.',
                'services' => ['Bridal gowns', 'Reception wear', 'Veil and accessory design', 'Custom fittings'],
                'portfolio_images' => [
                    ['image' => 'images/creative/portfolio/design-gown-atelier.jpg', 'caption' => 'Gown on atelier dress form'],
                    ['image' => 'images/gallery/s3-06.jpg', 'caption' => 'Runway bridal finale'],
                ],
                'website_url' => 'https://idfr.example/designers/anjali-thomas',
                'instagram_url' => 'https://instagram.com/anjalithomasatelier',
                'is_featured' => true,
                'display_order' => 5,
                'status' => 'published',
                'seasons' => [2, 3, 4],
            ],
            [
                'name' => 'Rohan Sen',
                'brand_name' => 'Sen & Co',
                'slug' => 'rohan-sen',
                'profile_photo' => 'images/gallery/s2-05.jpg',
                'brand_logo' => 'images/logo/idfr-mark.png',
                'location' => 'Kolkata, West Bengal',
                'experience' => '10 years',
                'short_intro' => 'Timeless Dhakai Jamdani and handcrafted silhouettes for the global runway.',
                'biography' => 'Rohan Sen champions heritage Indian handcrafts and has represented Indian couture at multiple international fashion councils before joining IDFR.',
                'collection_title' => 'Jamdani Reborn',
                'collection_description' => 'Light-as-air Jamdani weaves crafted into modern trench dresses and asymmetric jackets.',
                'services' => ['Heritage couture', 'Handloom advocacy', 'Custom orders', 'Runway showcases'],
                'portfolio_images' => [
                    ['image' => 'images/gallery/s2-05.jpg', 'caption' => 'Jamdani drape'],
                    ['image' => 'images/gallery/s1-04.jpg', 'caption' => 'Textile showcase'],
                ],
                'website_url' => 'https://idfr.example/designers/rohan-sen',
                'instagram_url' => 'https://instagram.com/senandco',
                'is_featured' => false,
                'display_order' => 6,
                'status' => 'published',
                'seasons' => [1, 2, 3, 4],
            ],
        ];

        foreach ($designersData as $dData) {
            $seasons = $dData['seasons'];
            unset($dData['seasons']);

            $designer = Designer::updateOrCreate(
                ['slug' => $dData['slug']],
                $dData
            );

            $seasonIds = [];
            foreach ($seasons as $sNum) {
                if (isset($seasonModels[$sNum])) {
                    $seasonIds[] = $seasonModels[$sNum]->id;
                }
            }
            $designer->seasons()->sync($seasonIds);
        }
    }
}

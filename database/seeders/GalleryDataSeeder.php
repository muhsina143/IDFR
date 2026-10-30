<?php

namespace Database\Seeders;

use App\Models\ContactEnquiry;
use App\Models\GalleryImage;
use App\Models\Season;
use Illuminate\Database\Seeder;

class GalleryDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = Season::all()->keyBy('number');

        $items = [
            // Season 1
            [
                'season_number' => 1,
                'season_name' => 'Season 1',
                'title' => 'The Opening Walk',
                'alt_text' => 'Model posing in an editorial ensemble',
                'image_path' => 'images/gallery/s1-01.jpg',
                'width' => 1000,
                'height' => 1500,
                'display_order' => 1,
            ],
            [
                'season_number' => 1,
                'season_name' => 'Season 1',
                'title' => 'Designer Spotlight',
                'alt_text' => 'Model in a statement outfit with sunglasses',
                'image_path' => 'images/gallery/s1-02.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 2,
            ],
            [
                'season_number' => 1,
                'season_name' => 'Season 1',
                'title' => 'Front Row',
                'alt_text' => 'Audience seated for the Season 1 show',
                'image_path' => 'images/gallery/s1-03.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 3,
            ],
            [
                'season_number' => 1,
                'season_name' => 'Season 1',
                'title' => 'Backstage — Hair & Styling',
                'alt_text' => 'Hair stylist preparing a model backstage',
                'image_path' => 'images/gallery/s1-04.jpg',
                'width' => 1000,
                'height' => 668,
                'display_order' => 4,
            ],
            [
                'season_number' => 1,
                'season_name' => 'Season 1',
                'title' => 'The Collection Rail',
                'alt_text' => 'Garments from the designer collection on a rail',
                'image_path' => 'images/gallery/s1-05.jpg',
                'width' => 1000,
                'height' => 750,
                'display_order' => 5,
            ],
            [
                'season_number' => 1,
                'season_name' => 'Season 1',
                'title' => 'Finale Florals',
                'alt_text' => 'Model in a floral dress at the Season 1 finale',
                'image_path' => 'images/gallery/s1-06.jpg',
                'width' => 1000,
                'height' => 665,
                'display_order' => 6,
            ],

            // Season 2
            [
                'season_number' => 2,
                'season_name' => 'Season 2',
                'title' => 'Street to Stage',
                'alt_text' => 'Model walking in a long coat',
                'image_path' => 'images/gallery/s2-01.jpg',
                'width' => 1000,
                'height' => 1504,
                'display_order' => 7,
            ],
            [
                'season_number' => 2,
                'season_name' => 'Season 2',
                'title' => 'The Gown Edit',
                'alt_text' => 'Model in an evening gown',
                'image_path' => 'images/gallery/s2-02.jpg',
                'width' => 1000,
                'height' => 1500,
                'display_order' => 8,
            ],
            [
                'season_number' => 2,
                'season_name' => 'Season 2',
                'title' => 'Backstage — Makeup',
                'alt_text' => 'Makeup being applied backstage',
                'image_path' => 'images/gallery/s2-03.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 9,
            ],
            [
                'season_number' => 2,
                'season_name' => 'Season 2',
                'title' => 'Under the Lights',
                'alt_text' => 'Crowd and stage lights at the Season 2 show',
                'image_path' => 'images/gallery/s2-04.jpg',
                'width' => 1000,
                'height' => 750,
                'display_order' => 10,
            ],
            [
                'season_number' => 2,
                'season_name' => 'Season 2',
                'title' => 'Atelier Preview',
                'alt_text' => 'Designer garments displayed before the show',
                'image_path' => 'images/gallery/s2-05.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 11,
            ],
            [
                'season_number' => 2,
                'season_name' => 'Season 2',
                'title' => 'Golden Hour Portrait',
                'alt_text' => 'Portrait of a model in soft light',
                'image_path' => 'images/gallery/s2-06.jpg',
                'width' => 1000,
                'height' => 1000,
                'display_order' => 12,
            ],

            // Season 3
            [
                'season_number' => 3,
                'season_name' => 'Season 3',
                'title' => 'Polka & Poise',
                'alt_text' => 'Model in a polka-dot dress',
                'image_path' => 'images/gallery/s3-01.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 13,
            ],
            [
                'season_number' => 3,
                'season_name' => 'Season 3',
                'title' => 'Resort Collection',
                'alt_text' => 'Model in a resort-wear outfit',
                'image_path' => 'images/gallery/s3-02.jpg',
                'width' => 1000,
                'height' => 1504,
                'display_order' => 14,
            ],
            [
                'season_number' => 3,
                'season_name' => 'Season 3',
                'title' => 'The Grand Finale',
                'alt_text' => 'Confetti falling over the audience at the finale',
                'image_path' => 'images/gallery/s3-03.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 15,
            ],
            [
                'season_number' => 3,
                'season_name' => 'Season 3',
                'title' => 'Behind the Lens',
                'alt_text' => 'Model in sunglasses during a photo call',
                'image_path' => 'images/gallery/s3-04.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 16,
            ],
            [
                'season_number' => 3,
                'season_name' => 'Season 3',
                'title' => 'The Long Walk',
                'alt_text' => 'Model walking through an arcade',
                'image_path' => 'images/gallery/s3-05.jpg',
                'width' => 1000,
                'height' => 1500,
                'display_order' => 17,
            ],
            [
                'season_number' => 3,
                'season_name' => 'Season 3',
                'title' => 'Designer Showroom',
                'alt_text' => 'Interior of a designer showroom',
                'image_path' => 'images/gallery/s3-06.jpg',
                'width' => 1000,
                'height' => 667,
                'display_order' => 18,
            ],
        ];

        foreach ($items as $data) {
            $seasonId = isset($seasons[$data['season_number']]) ? $seasons[$data['season_number']]->id : null;
            GalleryImage::firstOrCreate(
                ['image_path' => $data['image_path']],
                [
                    'season_id' => $seasonId,
                    'season_name' => $data['season_name'],
                    'title' => $data['title'],
                    'alt_text' => $data['alt_text'],
                    'width' => $data['width'],
                    'height' => $data['height'],
                    'display_order' => $data['display_order'],
                    'is_featured' => true,
                    'status' => 'published',
                ]
            );
        }

        // Seed an initial contact enquiry for test realism
        if (ContactEnquiry::count() === 0) {
            ContactEnquiry::create([
                'name' => 'Meera Radhakrishnan',
                'phone' => '+91 98470 55432',
                'email' => 'meera.couture@example.com',
                'subject' => 'Season 4 Designer Showcase Application',
                'message' => 'Hello IDFR team, we would like to present our upcoming handloom couture line at Season 4 in Kozhikode. Kindly share the designer onboarding deck.',
                'status' => 'unread',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ]);
        }
    }
}

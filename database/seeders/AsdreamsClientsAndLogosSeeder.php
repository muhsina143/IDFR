<?php

namespace Database\Seeders;

use App\Models\Asdreams\AsdreamsClient;
use App\Models\Asdreams\AsdreamsMovingLogo;
use Illuminate\Database\Seeder;

class AsdreamsClientsAndLogosSeeder extends Seeder
{
    /**
     * Run the database seeds for ASDreams Clients and Moving Client Logos independently.
     */
    public function run(): void
    {
        if (AsdreamsClient::count() === 0) {
            $clients = [
                [
                    'name' => 'Maison Kerala',
                    'category' => 'Couture House',
                    'featured_image' => 'images/clients/client-featured.jpg',
                    'logo_style' => 'arch',
                    'logo_mark' => 'M',
                    'logo_word' => 'MAISON',
                    'logo_sub' => 'KERALA',
                    'description' => 'IDFR helped us cast our entire festive campaign in a single week. The talent was professional, prepared and camera-ready.',
                    'author_name' => 'Neha Varghese',
                    'author_role' => 'Creative Director',
                    'is_featured' => true,
                    'display_order' => 1,
                    'is_active' => true,
                ],
                [
                    'name' => 'Vasa Bridal',
                    'category' => 'Bridal Wear',
                    'featured_image' => 'images/clients/client-01.jpg',
                    'logo_style' => 'diamond',
                    'logo_mark' => 'V',
                    'logo_word' => 'VASA',
                    'logo_sub' => 'BRIDAL',
                    'description' => 'Every bride in our lookbook was an IDFR model. Their portfolios made shortlisting effortless.',
                    'author_name' => 'Lakshmi Prasad',
                    'author_role' => 'Founder',
                    'is_featured' => false,
                    'display_order' => 2,
                    'is_active' => true,
                ],
                [
                    'name' => 'Urban Thread',
                    'category' => 'Streetwear',
                    'featured_image' => 'images/clients/client-02.jpg',
                    'logo_style' => 'bars',
                    'logo_mark' => 'U',
                    'logo_word' => 'URBAN',
                    'logo_sub' => 'THREAD',
                    'description' => 'We found fresh, authentic faces for our youth line — exactly the energy our brand needed.',
                    'author_name' => 'Karan Shah',
                    'author_role' => 'Brand Manager',
                    'is_featured' => false,
                    'display_order' => 3,
                    'is_active' => true,
                ],
                [
                    'name' => 'Lumen Magazine',
                    'category' => 'Fashion Publication',
                    'featured_image' => 'images/gallery/s2-06.jpg',
                    'logo_style' => 'circle',
                    'logo_mark' => 'L',
                    'logo_word' => 'LUMEN',
                    'logo_sub' => 'MAGAZINE',
                    'description' => 'Our cover stars from IDFR consistently deliver striking editorial work. It is our first stop for new faces.',
                    'author_name' => 'Rhea Kapoor',
                    'author_role' => 'Fashion Editor',
                    'is_featured' => false,
                    'display_order' => 4,
                    'is_active' => true,
                ],
                [
                    'name' => 'Prism Advertising',
                    'category' => 'Advertising Agency',
                    'featured_image' => 'images/gallery/s3-04.jpg',
                    'logo_style' => 'hex',
                    'logo_mark' => 'P',
                    'logo_word' => 'PRISM',
                    'logo_sub' => 'ADVERTISING',
                    'description' => 'From TV commercials to digital films, IDFR talent has elevated campaigns for our biggest accounts.',
                    'author_name' => 'Siddharth Rao',
                    'author_role' => 'Casting Head',
                    'is_featured' => false,
                    'display_order' => 5,
                    'is_active' => true,
                ],
                [
                    'name' => 'Kanji Silks',
                    'category' => 'Heritage Textiles',
                    'featured_image' => 'images/partners/partner-03.jpg',
                    'logo_style' => 'weave',
                    'logo_mark' => 'K',
                    'logo_word' => 'KANJI',
                    'logo_sub' => 'SILKS',
                    'description' => 'IDFR models carry our heritage silks with grace. Their runway training shows in every frame.',
                    'author_name' => 'Meenakshi Sundaram',
                    'author_role' => 'Managing Director',
                    'is_featured' => false,
                    'display_order' => 6,
                    'is_active' => true,
                ],
                [
                    'name' => 'Northstar Footwear',
                    'category' => 'Footwear',
                    'featured_image' => 'images/gallery/s2-01.jpg',
                    'logo_style' => 'star',
                    'logo_mark' => 'N',
                    'logo_word' => 'NORTHSTAR',
                    'logo_sub' => 'FOOTWEAR',
                    'description' => 'Booking models through IDFR is fast and transparent. We now cast every season with them.',
                    'author_name' => 'Imran Qureshi',
                    'author_role' => 'Marketing Lead',
                    'is_featured' => false,
                    'display_order' => 7,
                    'is_active' => true,
                ],
                [
                    'name' => 'Aura Cosmetics',
                    'category' => 'Beauty',
                    'featured_image' => 'images/gallery/s2-03.jpg',
                    'logo_style' => 'drop',
                    'logo_mark' => 'A',
                    'logo_word' => 'AURA',
                    'logo_sub' => 'COSMETICS',
                    'description' => 'The beauty talent on IDFR is outstanding — flawless skin, great expressions and total professionalism.',
                    'author_name' => 'Fatima Sheikh',
                    'author_role' => 'Head of Brand',
                    'is_featured' => false,
                    'display_order' => 8,
                    'is_active' => true,
                ],
            ];

            foreach ($clients as $clientData) {
                AsdreamsClient::create($clientData);
            }
        }

        if (AsdreamsMovingLogo::count() === 0) {
            $movingLogos = [
                ['name' => 'Maison Kerala', 'logo_style' => 'arch', 'logo_mark' => 'M', 'logo_word' => 'MAISON', 'logo_sub' => 'KERALA', 'display_order' => 1, 'is_active' => true],
                ['name' => 'Vasa Bridal', 'logo_style' => 'diamond', 'logo_mark' => 'V', 'logo_word' => 'VASA', 'logo_sub' => 'BRIDAL', 'display_order' => 2, 'is_active' => true],
                ['name' => 'Urban Thread', 'logo_style' => 'bars', 'logo_mark' => 'U', 'logo_word' => 'URBAN', 'logo_sub' => 'THREAD', 'display_order' => 3, 'is_active' => true],
                ['name' => 'Lumen Magazine', 'logo_style' => 'circle', 'logo_mark' => 'L', 'logo_word' => 'LUMEN', 'logo_sub' => 'MAGAZINE', 'display_order' => 4, 'is_active' => true],
                ['name' => 'Prism Advertising', 'logo_style' => 'hex', 'logo_mark' => 'P', 'logo_word' => 'PRISM', 'logo_sub' => 'ADVERTISING', 'display_order' => 5, 'is_active' => true],
                ['name' => 'Kanji Silks', 'logo_style' => 'weave', 'logo_mark' => 'K', 'logo_word' => 'KANJI', 'logo_sub' => 'SILKS', 'display_order' => 6, 'is_active' => true],
                ['name' => 'Northstar Footwear', 'logo_style' => 'star', 'logo_mark' => 'N', 'logo_word' => 'NORTHSTAR', 'logo_sub' => 'FOOTWEAR', 'display_order' => 7, 'is_active' => true],
                ['name' => 'Aura Cosmetics', 'logo_style' => 'drop', 'logo_mark' => 'A', 'logo_word' => 'AURA', 'logo_sub' => 'COSMETICS', 'display_order' => 8, 'is_active' => true],
            ];

            foreach ($movingLogos as $logoData) {
                AsdreamsMovingLogo::create($logoData);
            }
        }
    }
}

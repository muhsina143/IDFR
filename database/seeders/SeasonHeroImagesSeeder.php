<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\SeasonImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SeasonHeroImagesSeeder extends Seeder
{
    /**
     * Import existing identifiable homepage hero images into season_images.
     */
    public function run(): void
    {
        // 1. Ensure Season 8 exists and is published
        $season8 = Season::firstOrCreate(
            ['number' => 8],
            [
                'title' => 'Season 8',
                'year' => '2026',
                'location' => 'Kochi, Kerala',
                'dates' => '18 — 20 December 2026',
                'status' => 'published',
                'description' => 'Season 8 brings together emerging talent, established models, designers and fashion enthusiasts on one remarkable stage.',
            ]
        );

        // 2. Identify all hero images used in IDFR Season 8
        $heroImages = [
            [
                'image_path' => 'images/hero/hero-01.jpg',
                'look_number' => 'Look 01',
                'image_title' => 'Neon Noir',
                'caption' => 'Neon Noir',
                'designer_name' => 'Aarav Khanna',
                'alt_text' => 'Model in a dramatic wide-leg ensemble under stage lights',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-02.jpg',
                'look_number' => 'Look 02',
                'image_title' => 'Golden Hour Tailoring',
                'caption' => 'Golden Hour Tailoring',
                'designer_name' => 'Meher Sinha',
                'alt_text' => 'Model in a tailored trouser suit and sunglasses',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-03.jpg',
                'look_number' => 'Look 03',
                'image_title' => 'Heritage Overcoat',
                'caption' => 'Heritage Overcoat',
                'designer_name' => 'Nikhil Joseph',
                'alt_text' => 'Model wearing a long overcoat in front of historic architecture',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-04.jpg',
                'look_number' => 'Look 04',
                'image_title' => 'The Twirl Gown',
                'caption' => 'The Twirl Gown',
                'designer_name' => 'Ritu Anand',
                'alt_text' => 'Model twirling in a flowing gown',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-05.jpg',
                'look_number' => 'Look 05',
                'image_title' => 'Modern Gentleman',
                'caption' => 'Modern Gentleman',
                'designer_name' => 'Kunal Sethi',
                'alt_text' => 'Male model in a sharp double-breasted suit',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-06.jpg',
                'look_number' => 'Look 06',
                'image_title' => 'Velvet Evening',
                'caption' => 'Velvet Evening',
                'designer_name' => 'Leena D\'Souza',
                'alt_text' => 'Model in an off-shoulder velvet evening gown',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-07.jpg',
                'look_number' => 'Look 07',
                'image_title' => 'Emerald Silk',
                'caption' => 'Emerald Silk',
                'designer_name' => 'Anjali Menon',
                'alt_text' => 'Model walking the ramp with confident poise',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-08.png',
                'look_number' => 'Look 08',
                'image_title' => 'Champagne Silhouette',
                'caption' => 'Champagne Silk Slit Gown',
                'designer_name' => 'IDFR Runway Couture',
                'alt_text' => 'Model in an elegant champagne silk cowl neck dress',
                'display_order' => 8,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/hero/hero-09.jpg',
                'look_number' => 'Look 09',
                'image_title' => 'Coastal Muse',
                'caption' => 'Coastal Muse',
                'designer_name' => 'Anjali Menon',
                'alt_text' => 'Model in a flowing white dress by the sea',
                'display_order' => 9,
                'is_active' => true,
            ],
        ];

        $imported = 0;
        foreach ($heroImages as $imgData) {
            $fullPath = public_path($imgData['image_path']);
            if (! File::exists($fullPath)) {
                $this->command?->warn("Warning: Source image file not found at: {$imgData['image_path']}");
            }

            SeasonImage::updateOrCreate(
                [
                    'season_id' => $season8->id,
                    'image_path' => $imgData['image_path'],
                ],
                [
                    'look_number' => $imgData['look_number'],
                    'image_title' => $imgData['image_title'],
                    'caption' => $imgData['caption'],
                    'designer_name' => $imgData['designer_name'],
                    'alt_text' => $imgData['alt_text'],
                    'display_order' => $imgData['display_order'],
                    'is_active' => $imgData['is_active'],
                ]
            );
            $imported++;
        }

        $this->command?->info("Successfully imported {$imported} Season 8 hero images into season_images table.");
    }
}

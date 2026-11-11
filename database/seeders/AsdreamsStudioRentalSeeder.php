<?php

namespace Database\Seeders;

use App\Models\Asdreams\AsdreamsStudioEquipment;
use App\Models\Asdreams\AsdreamsStudioFacility;
use App\Models\Asdreams\AsdreamsStudioGalleryImage;
use App\Models\Asdreams\AsdreamsStudioRentalSetting;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Database\Seeder;

class AsdreamsStudioRentalSeeder extends Seeder
{
    /**
     * Seed initial Facilities Provided, Equipment Included, Studio Gallery, and Pricing settings.
     */
    public function run(): void
    {
        $defaultSettings = [
            'studio_facilities_heading' => 'Facilities Provided',
            'studio_facilities_description' => 'All areas are air-conditioned.',
            'studio_equipment_heading' => 'Equipment Included',
            'studio_equipment_description' => 'Available with every studio rental booking.',
        ];

        foreach ($defaultSettings as $key => $value) {
            if (AsdreamsWebsiteSetting::where('key', $key)->doesntExist()) {
                AsdreamsWebsiteSetting::set($key, $value, 'studio_rental');
            }
        }

        AsdreamsStudioRentalSetting::ensureDefaults();

        if (AsdreamsStudioFacility::count() === 0) {
            $facilities = [
                ['name' => 'Lobby', 'icon' => 'door', 'display_order' => 1, 'is_active' => true],
                ['name' => 'Makeup Room', 'icon' => 'sparkles', 'display_order' => 2, 'is_active' => true],
                ['name' => 'Changing Room', 'icon' => 'hanger', 'display_order' => 3, 'is_active' => true],
                ['name' => 'Pantry', 'icon' => 'cup', 'display_order' => 4, 'is_active' => true],
                ['name' => 'Photography Room', 'icon' => 'camera', 'display_order' => 5, 'is_active' => true],
                ['name' => '2 Restrooms', 'icon' => 'restroom', 'display_order' => 6, 'is_active' => true],
            ];

            foreach ($facilities as $facility) {
                AsdreamsStudioFacility::create($facility);
            }
        }

        if (AsdreamsStudioEquipment::count() === 0) {
            $equipmentItems = [
                ['name' => '2 SK 400 Photo Lights', 'icon' => 'light', 'display_order' => 1, 'is_active' => true],
                ['name' => '1 AD 400 Photo Light', 'icon' => 'light', 'display_order' => 2, 'is_active' => true],
                ['name' => '2 SL 100 Video Lights', 'icon' => 'light', 'display_order' => 3, 'is_active' => true],
                ['name' => '4 Stick Lights (Video Lights)', 'icon' => 'light', 'display_order' => 4, 'is_active' => true],
                ['name' => 'Blue Sofa', 'icon' => 'sofa', 'display_order' => 5, 'is_active' => true],
                ['name' => 'Model Stool', 'icon' => 'check', 'display_order' => 6, 'is_active' => true],
                ['name' => 'Table and Chairs', 'icon' => 'check', 'display_order' => 7, 'is_active' => true],
            ];

            foreach ($equipmentItems as $equipment) {
                AsdreamsStudioEquipment::create($equipment);
            }
        }

        if (AsdreamsStudioGalleryImage::count() === 0) {
            $galleryImages = [
                [
                    'title' => 'Main Floor',
                    'description' => 'Photography studio set with softboxes and a white backdrop',
                    'image_path' => 'images/asdreams/hero-08.jpg',
                    'display_order' => 1,
                    'is_active' => true,
                ],
                [
                    'title' => 'Studio Interior',
                    'description' => 'ASDreamStudio interior production space',
                    'image_path' => 'images/asdreams/about-studio.jpg',
                    'display_order' => 2,
                    'is_active' => true,
                ],
                [
                    'title' => 'Lighting Kit',
                    'description' => 'Cinema camera and lighting kit on a studio set',
                    'image_path' => 'images/creative/portfolio/video-cinema-camera.jpg',
                    'display_order' => 3,
                    'is_active' => true,
                ],
                [
                    'title' => 'Prep Suite',
                    'description' => 'Prep suite with monitors ready for production',
                    'image_path' => 'images/creative/portfolio/video-edit-suite.jpg',
                    'display_order' => 4,
                    'is_active' => true,
                ],
                [
                    'title' => 'Portrait Bay',
                    'description' => 'Studio portrait backdrop with soft lighting',
                    'image_path' => 'images/asdreams/gallery/portrait-01.jpg',
                    'display_order' => 5,
                    'is_active' => true,
                ],
                [
                    'title' => 'Set Lighting',
                    'description' => 'Stage lighting rig for video production',
                    'image_path' => 'images/creative/portfolio/video-stage-rig.jpg',
                    'display_order' => 6,
                    'is_active' => true,
                ],
            ];

            foreach ($galleryImages as $image) {
                AsdreamsStudioGalleryImage::create($image);
            }
        }
    }
}

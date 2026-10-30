<?php

namespace App\Enums;

enum UserRole: string
{
    case Model = 'model';
    case MakeupArtist = 'makeup-artist';
    case Hairstylist = 'hairstylist';
    case Photographer = 'photographer';
    case Videographer = 'videographer';
    case FashionDesigner = 'fashion-designer';
    case DronePilot = 'drone-pilot';
    case PhotoVideoEditor = 'photo-video-editor';
    case JewelleryDesigner = 'jewellery-designer';

    /**
     * Get all registration role string values.
     *
     * @return array<string>
     */
    public static function registrationValues(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Model => 'Model',
            self::MakeupArtist => 'Makeup Artist',
            self::Hairstylist => 'Hairstylist',
            self::Photographer => 'Photographer',
            self::Videographer => 'Videographer',
            self::FashionDesigner => 'Fashion Designer',
            self::DronePilot => 'Drone Pilot',
            self::PhotoVideoEditor => 'Photo & Video Editor',
            self::JewelleryDesigner => 'Jewellery Designer',
        };
    }

    /**
     * Get the corresponding profile or action route name for the role.
     */
    public function profileRouteName(): string
    {
        return match ($this) {
            self::Model => 'profile.model',
            self::MakeupArtist => 'profile.makeup-artist',
            self::Hairstylist => 'profile.hairstylist',
            self::Photographer => 'profile.photographer',
            self::Videographer => 'profile.videographer',
            self::FashionDesigner => 'profile.fashion-designer',
            self::DronePilot => 'profile.drone-pilot',
            self::PhotoVideoEditor => 'profile.photo-video-editor',
            self::JewelleryDesigner => 'profile.jewellery-designer',
        };
    }
}

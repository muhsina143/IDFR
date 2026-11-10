<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsAboutContent extends Model
{
    use HasFactory;

    protected $table = 'asdreams_about_contents';

    protected $fillable = [
        'banner_image',
        'title',
        'video_cover_image',
        'video_url',
        'years_of_experience',
        'how_we_work_title',
        'how_we_work_text',
        'story_heading_main',
        'story_heading_highlight',
        'story_paragraph_1',
        'story_bullets',
        'story_extended_text',
        'studio_image',
        'studio_caption_title',
        'studio_caption_subtitle',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'story_bullets' => 'array',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'banner_image' => 'images/asdreams/ab2.png',
            'title' => 'About Us',
            'video_cover_image' => 'images/asdreams/about-video.jpg',
            'video_url' => '',
            'years_of_experience' => 32,
            'how_we_work_title' => 'How we work?',
            'how_we_work_text' => 'From the first mood board to the final colour grade, one dedicated team plans, shoots and edits every story — so nothing gets lost between the shutter and the screen.',
            'story_heading_main' => 'Behind the Lens,',
            'story_heading_highlight' => 'Ahead of the Curve',
            'story_paragraph_1' => "ASDreamStudio has photographed and filmed every IDFR season since the very first runway. We pair editorial instinct with cinema-grade gear to turn fleeting moments — a designer's final bow, a first look, a sunrise vow — into images that last a lifetime.",
            'story_bullets' => [
                'Runway, backstage and campaign coverage for every IDFR season',
                'Cinema cameras, drones and a fully equipped lighting studio',
                'Same-week edits, colour grading and print-ready albums',
            ],
            'story_extended_text' => 'Our Kozhikode studio houses a 2,400 sq ft cyclorama, a dedicated edit suite and a styling lounge. Beyond IDFR, we travel across India and abroad for weddings, destination shoots, brand films and live events — always with the same small, senior crew.',
            'studio_image' => 'images/asdreams/about-studio.jpg',
            'studio_caption_title' => 'Studio 01',
            'studio_caption_subtitle' => 'Kozhikode, Kerala',
        ]);
    }
}

<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsContactSetting extends Model
{
    use HasFactory;

    protected $table = 'asdreams_contact_settings';

    protected $fillable = [
        'section_label',
        'title',
        'title_highlight',
        'intro',
        'studio_address',
        'maps_url',
        'studio_phone',
        'bookings_phone',
        'general_email',
        'bookings_email',
        'working_hours',
        'note',
    ];

    protected $casts = [
        'working_hours' => 'array',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'section_label' => 'Get in Touch',
            'title' => 'Let’s create',
            'title_highlight' => 'together',
            'intro' => 'Planning a wedding, a fashion shoot or a brand film? Share a few details and our team will get back to you within one working day.',
            'studio_address' => "ASDreamStudio, 2nd Floor, Mavoor Road,\nKozhikode, Kerala 673004, India",
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Mavoor+Road+Kozhikode+Kerala',
            'studio_phone' => '+91 98765 00123',
            'bookings_phone' => '+91 98470 00456',
            'general_email' => 'hello@asdreamstudio.example',
            'bookings_email' => 'bookings@asdreamstudio.example',
            'working_hours' => [
                ['days' => 'Monday – Friday', 'hours' => '10:00 AM – 7:00 PM'],
                ['days' => 'Saturday', 'hours' => '10:00 AM – 5:00 PM'],
                ['days' => 'Sunday', 'hours' => 'By appointment'],
            ],
            'note' => 'On-location shoots run seven days a week, including sunrise and late-night sessions.',
        ]);
    }
}

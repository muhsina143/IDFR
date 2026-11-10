<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsShootBooking extends Model
{
    use HasFactory;

    protected $table = 'asdreams_shoot_bookings';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'shoot_date',
        'subject',
        'message',
        'shoot_type',
        'booking_type',
        'purpose',
        'days',
        'start_date',
        'end_date',
        'status',
        'admin_notes',
        'ip_address',
    ];

    protected $casts = [
        'shoot_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'integer',
    ];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }

    public function scopeStudio($query)
    {
        return $query->where(function ($q) {
            $q->where('booking_type', 'studio')
                ->orWhere('shoot_type', 'Studio Rental');
        });
    }

    public function scopeShoot($query)
    {
        return $query->where(function ($q) {
            $q->where('booking_type', 'shoot')
                ->orWhereNull('booking_type');
        })->where(function ($q) {
            $q->where('shoot_type', '!=', 'Studio Rental')
                ->orWhereNull('shoot_type');
        });
    }
}

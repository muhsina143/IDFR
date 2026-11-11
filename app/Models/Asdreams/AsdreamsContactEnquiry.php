<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsContactEnquiry extends Model
{
    use HasFactory;
    use HasReadTracking;

    protected $table = 'asdreams_contact_enquiries';

    protected $fillable = [
        'brand',
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'status',
        'read_at',
        'admin_notes',
        'ip_address',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeRecent($query)
    {
        return $query->latest();
    }
}

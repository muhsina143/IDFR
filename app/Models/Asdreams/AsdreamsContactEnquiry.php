<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsdreamsContactEnquiry extends Model
{
    use HasFactory;

    protected $table = 'asdreams_contact_enquiries';

    protected $fillable = [
        'brand',
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'status',
        'admin_notes',
        'ip_address',
    ];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }
}

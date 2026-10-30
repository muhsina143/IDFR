<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEnquiry extends Model
{
    use HasFactory;

    protected $table = 'contact_enquiries';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];

    /**
     * Scope for unread enquiries.
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope for recent enquiries.
     */
    public function scopeRecent($query)
    {
        return $query->latest();
    }
}

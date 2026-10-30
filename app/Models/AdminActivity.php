<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class AdminActivity extends Model
{
    protected $fillable = [
        'user_id',
        'admin_email',
        'activity',
        'description',
        'ip_address',
        'user_agent',
    ];

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an administrative event.
     */
    public static function log(string $activity, mixed $arg2 = null, mixed $arg3 = null, ?string $description = null, ?Request $request = null): self
    {
        $req = $request ?: request();
        $user = auth()->user();
        $email = null;

        if ($arg2 instanceof User) {
            $user = $arg2;
            $email = is_string($arg3) ? $arg3 : ($user ? $user->email : null);
        } elseif (is_string($arg2)) {
            $description = $arg2;
            if (is_string($arg3)) {
                $email = $arg3;
            } elseif ($arg3 instanceof User) {
                $user = $arg3;
            }
        }

        if (! $email && $user) {
            $email = $user->email;
        }

        return self::create([
            'user_id' => $user ? $user->id : null,
            'admin_email' => $email,
            'activity' => $activity,
            'description' => $description,
            'ip_address' => $req ? $req->ip() : null,
            'user_agent' => $req ? substr((string) $req->userAgent(), 0, 500) : null,
        ]);
    }
}

<?php

namespace App\Models\Asdreams;

use Illuminate\Database\Eloquent\Builder;

trait HasReadTracking
{
    /**
     * Scope records that have not been read yet.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at')->where('status', 'new');
    }

    /**
     * Scope legacy "new" queries using unread semantics.
     */
    public function scopeNew(Builder $query): Builder
    {
        return $this->scopeUnread($query);
    }

    /**
     * Mark an unread record as read when opened, without altering already-read records.
     */
    public function markAsRead(): void
    {
        if ($this->read_at !== null && $this->status !== 'new') {
            return;
        }

        if ($this->read_at === null) {
            $this->read_at = now();
        }

        if ($this->status === 'new') {
            $this->status = 'read';
        }

        $this->save();
    }

    /**
     * Update status and admin notes while keeping read_at consistent with manual status changes.
     *
     * @param  array{status: string, admin_notes?: string|null}  $validated
     */
    public function applyAdminStatusUpdate(array $validated): void
    {
        $payload = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ];

        if ($validated['status'] === 'new') {
            $payload['read_at'] = null;
        } elseif ($this->read_at === null) {
            $payload['read_at'] = now();
        }

        $this->update($payload);
    }
}

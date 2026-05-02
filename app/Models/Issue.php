<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'category',
        'status',
        'ai_summary',
        'ai_next_action',
        'is_escalated',
        'escalation_reason',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'priority'     => Priority::class,
            'category'     => Category::class,
            'status'       => Status::class,
            'is_escalated' => 'boolean',
            'resolved_at'  => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(IssueStatusChange::class)->latest();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if ($status && in_array($status, Status::values())) {
            $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByPriority(Builder $query, ?string $priority): Builder
    {
        if ($priority && in_array($priority, Priority::values())) {
            $query->where('priority', $priority);
        }
        return $query;
    }

    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        if ($category && in_array($category, Category::values())) {
            $query->where('category', $category);
        }
        return $query;
    }

    public function scopeEscalated(Builder $query): Builder
    {
        return $query->where('is_escalated', true);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->hasElevatedAccess()) {
            return $query;
        }
        return $query->where('user_id', $user->id);
    }

    // ─── Business Logic ───────────────────────────────────────────────────────

    /**
     * Determine if this issue should be escalated.
     * Rules:
     *   - Critical priority → always escalate
     *   - Security category → always escalate
     *   - High priority + open for > 48 hours → escalate
     */
    public function shouldEscalate(): bool
    {
        if ($this->priority === Priority::Critical) {
            return true;
        }

        if ($this->category === Category::Security && $this->status->isOpen()) {
            return true;
        }

        if ($this->priority === Priority::High && $this->status->isOpen()) {
            $hoursSinceCreation = $this->created_at->diffInHours(now());
            return $hoursSinceCreation >= 48;
        }

        return false;
    }

    public function escalationReason(): ?string
    {
        if ($this->priority === Priority::Critical) {
            return 'Critical priority issue requires immediate attention.';
        }

        if ($this->category === Category::Security && $this->status->isOpen()) {
            return 'Security issues must be escalated regardless of priority.';
        }

        if ($this->priority === Priority::High && $this->status->isOpen()) {
            $hours = $this->created_at->diffInHours(now());
            if ($hours >= 48) {
                return "High-priority issue has been open for {$hours} hours without resolution.";
            }
        }

        return null;
    }

    public function refreshEscalation(): void
    {
        $shouldEscalate = $this->shouldEscalate();
        $this->update([
            'is_escalated'      => $shouldEscalate,
            'escalation_reason' => $shouldEscalate ? $this->escalationReason() : null,
        ]);
    }
}
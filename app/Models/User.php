<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => Role::class,
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(IssueStatusChange::class);
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isModerator(): bool
    {
        return $this->role === Role::Moderator;
    }

    public function isUser(): bool
    {
        return $this->role === Role::User;
    }

    public function hasElevatedAccess(): bool
    {
        return in_array($this->role, [Role::Admin, Role::Moderator]);
    }
}
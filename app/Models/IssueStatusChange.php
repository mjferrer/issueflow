<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueStatusChange extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'issue_id',
        'user_id',
        'from_status',
        'to_status',
        'note',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => Status::class,
            'to_status'   => Status::class,
            'changed_at'  => 'datetime',
        ];
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
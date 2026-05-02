@extends('layouts.app')

@section('title', 'All Issues')
@section('page-title', 'Issues')

@section('topbar-actions')
    <a href="{{ route('issues.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Issue
    </a>
@endsection

@section('content')

{{-- Stats Bar --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;">
    @php
        $user = auth()->user();
        $base = \App\Models\Issue::forUser($user);
        $totalCount    = (clone $base)->count();
        $openCount     = (clone $base)->where('status','open')->count();
        $criticalCount = (clone $base)->where('priority','critical')->where('status','open')->count();
        $escalatedCount = $user->hasElevatedAccess() ? \App\Models\Issue::escalated()->count() : 0;
    @endphp

    <div class="card" style="padding:16px 20px;">
        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Total Issues</div>
        <div style="font-family:'Space Mono',monospace;font-size:24px;font-weight:700;color:var(--text-primary);">{{ $totalCount }}</div>
    </div>
    <div class="card" style="padding:16px 20px;">
        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Open</div>
        <div style="font-family:'Space Mono',monospace;font-size:24px;font-weight:700;color:#93c5fd;">{{ $openCount }}</div>
    </div>
    <div class="card" style="padding:16px 20px;">
        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Critical</div>
        <div style="font-family:'Space Mono',monospace;font-size:24px;font-weight:700;color:#fca5a5;">{{ $criticalCount }}</div>
    </div>
    @if(auth()->user()->hasElevatedAccess())
    <div class="card" style="padding:16px 20px;{{ $escalatedCount > 0 ? 'border-color:rgba(239,68,68,0.3);background:rgba(239,68,68,0.05);' : '' }}">
        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Escalated</div>
        <div style="font-family:'Space Mono',monospace;font-size:24px;font-weight:700;color:{{ $escalatedCount > 0 ? '#fca5a5' : 'var(--text-secondary)' }};">{{ $escalatedCount }}</div>
    </div>
    @else
    <div class="card" style="padding:16px 20px;">
        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">My Issues</div>
        <div style="font-family:'Space Mono',monospace;font-size:24px;font-weight:700;color:var(--text-primary);">{{ auth()->user()->issues()->count() }}</div>
    </div>
    @endif
</div>

{{-- Filters --}}
<div class="card" style="padding:16px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('issues.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:120px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->value }}" {{ ($filters['status'] ?? '') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:120px;">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-input">
                <option value="">All Priorities</option>
                @foreach($priorities as $p)
                    <option value="{{ $p->value }}" {{ ($filters['priority'] ?? '') === $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:120px;">
            <label class="form-label">Category</label>
            <select name="category" class="form-input">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->value }}" {{ ($filters['category'] ?? '') === $c->value ? 'selected' : '' }}>{{ $c->label() }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(array_filter($filters))
                <a href="{{ route('issues.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Issues List --}}
<div class="card" style="overflow:hidden;">
    @forelse($issues as $issue)
    <a href="{{ route('issues.show', $issue) }}" class="issue-row" style="color:inherit;">
        {{-- Priority indicator --}}
        <div style="width:3px;flex-shrink:0;border-radius:2px;align-self:stretch;margin:2px 0;background:{{ match($issue->priority->value) { 'critical'=>'#ef4444','high'=>'#f97316','medium'=>'#f59e0b',default=>'#10b981' } }};"></div>

        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                <span class="badge badge-{{ $issue->priority->value }}">{{ $issue->priority->label() }}</span>
                <span class="badge badge-{{ $issue->status->value }}">{{ $issue->status->label() }}</span>
                <span style="font-size:11px;color:var(--text-muted);">{{ $issue->category->icon() }} {{ $issue->category->label() }}</span>
                @if($issue->is_escalated)
                    <span class="badge badge-escalated">⚠ ESCALATED</span>
                @endif
            </div>

            <div style="font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                #{{ $issue->id }} — {{ $issue->title }}
            </div>

            @if($issue->ai_summary)
            <div style="font-size:12px;color:var(--text-secondary);display:flex;gap:6px;align-items:flex-start;">
                <span style="color:#6366f1;flex-shrink:0;">AI:</span>
                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $issue->ai_summary }}</span>
            </div>
            @endif
        </div>

        <div style="flex-shrink:0;text-align:right;min-width:100px;">
            <div style="font-size:11px;color:var(--text-muted);">{{ $issue->user->name }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ $issue->created_at->diffForHumans() }}</div>
        </div>

        <svg style="flex-shrink:0;color:var(--text-muted);" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    @empty
    <div style="padding:48px;text-align:center;">
        <div style="font-size:36px;margin-bottom:12px;">📋</div>
        <div style="font-size:14px;color:var(--text-secondary);">No issues found.</div>
        <a href="{{ route('issues.create') }}" class="btn btn-primary" style="margin-top:16px;display:inline-flex;">Submit your first issue</a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($issues->hasPages())
<div style="margin-top:16px;display:flex;gap:8px;align-items:center;justify-content:space-between;">
    <div style="font-size:13px;color:var(--text-muted);">
        Showing {{ $issues->firstItem() }}–{{ $issues->lastItem() }} of {{ $issues->total() }} issues
    </div>
    <div style="display:flex;gap:4px;">
        @if($issues->onFirstPage())
            <span class="btn btn-secondary" style="opacity:0.4;cursor:not-allowed;">← Prev</span>
        @else
            <a href="{{ $issues->previousPageUrl() }}" class="btn btn-secondary">← Prev</a>
        @endif

        @if($issues->hasMorePages())
            <a href="{{ $issues->nextPageUrl() }}" class="btn btn-secondary">Next →</a>
        @else
            <span class="btn btn-secondary" style="opacity:0.4;cursor:not-allowed;">Next →</span>
        @endif
    </div>
</div>
@endif

@endsection
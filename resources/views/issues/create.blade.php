@extends('layouts.app')

@section('title', 'Submit Issue')
@section('page-title', 'Submit New Issue')

@section('topbar-actions')
    <a href="{{ route('issues.index') }}" class="btn btn-secondary">← Cancel</a>
@endsection

@section('content')

<div style="max-width:680px;">
    <div class="card" style="padding:28px;">

        @if($errors->any())
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:8px;padding:14px 16px;margin-bottom:24px;">
            <div style="font-size:13px;font-weight:600;color:#fca5a5;margin-bottom:8px;">Please fix the following errors:</div>
            <ul style="margin:0;padding-left:16px;">
                @foreach($errors->all() as $error)
                <li style="font-size:13px;color:#fca5a5;margin-bottom:3px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('issues.store') }}">
            @csrf

            <div style="margin-bottom:20px;">
                <label class="form-label" for="title">Issue Title <span style="color:#ef4444;">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-input"
                    value="{{ old('title') }}"
                    placeholder="Brief, descriptive title of the issue"
                    required
                >
                @error('title')
                    <div style="font-size:12px;color:#fca5a5;margin-top:5px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div>
                    <label class="form-label" for="priority">Priority <span style="color:#ef4444;">*</span></label>
                    <select id="priority" name="priority" class="form-input" required>
                        <option value="">Select priority...</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p->value }}" {{ old('priority') === $p->value ? 'selected' : '' }}>
                                {{ $p->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <div style="font-size:12px;color:#fca5a5;margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label" for="category">Category <span style="color:#ef4444;">*</span></label>
                    <select id="category" name="category" class="form-input" required>
                        <option value="">Select category...</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->value }}" {{ old('category') === $c->value ? 'selected' : '' }}>
                                {{ $c->icon() }} {{ $c->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <div style="font-size:12px;color:#fca5a5;margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label class="form-label" for="description">
                    Description <span style="color:#ef4444;">*</span>
                    <span style="color:var(--text-muted);font-weight:400;text-transform:none;letter-spacing:0;font-size:11px;"> — min 20 characters</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    class="form-input"
                    rows="6"
                    placeholder="Describe the issue in detail. Include: what happened, what you expected, steps to reproduce, and any relevant context..."
                    required
                    style="resize:vertical;"
                >{{ old('description') }}</textarea>
                @error('description')
                    <div style="font-size:12px;color:#fca5a5;margin-top:5px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- AI Notice --}}
            <div style="background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.15);border-radius:8px;padding:12px 16px;margin-bottom:24px;display:flex;gap:10px;align-items:flex-start;">
                <span style="background:#6366f1;color:white;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;padding:2px 6px;border-radius:3px;flex-shrink:0;margin-top:1px;">AI</span>
                <span style="font-size:12px;color:#9096a8;line-height:1.5;">An AI summary and suggested next action will be automatically generated from your description when you submit.</span>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    Submit Issue
                </button>
                <a href="{{ route('issues.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Priority Guide --}}
    <div class="card" style="padding:20px;margin-top:16px;">
        <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;">Priority Guide</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <div style="display:flex;gap:10px;align-items:flex-start;">
                <span class="badge badge-critical" style="flex-shrink:0;width:70px;justify-content:center;">Critical</span>
                <span style="font-size:12px;color:var(--text-muted);">Production down, data loss, security breach — immediate response required.</span>
            </div>
            <div style="display:flex;gap:10px;align-items:flex-start;">
                <span class="badge badge-high" style="flex-shrink:0;width:70px;justify-content:center;">High</span>
                <span style="font-size:12px;color:var(--text-muted);">Major functionality broken, customer-facing impact — resolve within 24 hours.</span>
            </div>
            <div style="display:flex;gap:10px;align-items:flex-start;">
                <span class="badge badge-medium" style="flex-shrink:0;width:70px;justify-content:center;">Medium</span>
                <span style="font-size:12px;color:var(--text-muted);">Feature degraded or workaround exists — resolve within this sprint.</span>
            </div>
            <div style="display:flex;gap:10px;align-items:flex-start;">
                <span class="badge badge-low" style="flex-shrink:0;width:70px;justify-content:center;">Low</span>
                <span style="font-size:12px;color:var(--text-muted);">Minor issue or improvement — schedule as bandwidth allows.</span>
            </div>
        </div>
    </div>
</div>

@endsection
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#0a0b0f]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Issue Tracker'); ?> — IssueFlow</title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #0a0b0f;
            --bg-card: #111318;
            --bg-card-hover: #161820;
            --bg-elevated: #1a1d26;
            --border: #252830;
            --border-subtle: #1c1f28;
            --text-primary: #e8eaf0;
            --text-secondary: #9096a8;
            --text-muted: #555c70;
            --accent: #6366f1;
            --accent-glow: rgba(99,102,241,0.15);
            --accent-hover: #818cf8;
            --red: #ef4444;
            --red-glow: rgba(239,68,68,0.1);
            --amber: #f59e0b;
            --green: #10b981;
            --blue: #3b82f6;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .font-mono { font-family: 'Space Mono', monospace; }

        /* Sidebar */
        .sidebar {
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            width: 240px;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 40;
        }

        .sidebar-logo {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-mark {
            width: 28px; height: 28px;
            background: var(--accent);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: white;
        }

        .logo-text {
            font-family: 'Space Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 0.05em;
        }

        .sidebar-nav {
            padding: 12px 12px;
            flex: 1;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            padding: 8px 12px 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.15s;
            margin-bottom: 1px;
        }

        .nav-item:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: var(--accent-glow);
            color: var(--accent-hover);
        }

        .nav-item .icon {
            width: 16px;
            height: 16px;
            opacity: 0.7;
        }

        .nav-item.active .icon { opacity: 1; }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border-subtle);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: capitalize;
        }

        /* Main content */
        .main { margin-left: 240px; min-height: 100vh; }

        .topbar {
            height: 56px;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .page-title {
            font-family: 'Space Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            flex: 1;
        }

        .content { padding: 28px; }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            font-family: 'Space Mono', monospace;
            padding: 2px 8px;
            border-radius: 4px;
            ring-width: 1px;
            border: 1px solid transparent;
        }

        .badge-critical { color: #fca5a5; background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.25); }
        .badge-high     { color: #fdba74; background: rgba(249,115,22,0.12); border-color: rgba(249,115,22,0.25); }
        .badge-medium   { color: #fcd34d; background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.25); }
        .badge-low      { color: #6ee7b7; background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.25); }

        .badge-open        { color: #93c5fd; background: rgba(59,130,246,0.12); border-color: rgba(59,130,246,0.25); }
        .badge-in_progress { color: #c4b5fd; background: rgba(139,92,246,0.12); border-color: rgba(139,92,246,0.25); }
        .badge-on_hold     { color: #fcd34d; background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.25); }
        .badge-resolved    { color: #6ee7b7; background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.25); }
        .badge-closed      { color: #94a3b8; background: rgba(100,116,139,0.12); border-color: rgba(100,116,139,0.25); }

        .badge-escalated { color: #fca5a5; background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.3); animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0%,100%{opacity:1} 50%{opacity:0.7} }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            border: none;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }
        .btn-primary:hover { background: #4f46e5; transform: translateY(-1px); }

        .btn-secondary {
            background: var(--bg-elevated);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { color: var(--text-primary); border-color: #3a3f52; }

        .btn-danger { background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }

        .btn-sm { padding: 4px 10px; font-size: 12px; }

        /* Form inputs */
        .form-input {
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 7px;
            color: var(--text-primary);
            font-size: 13px;
            padding: 8px 12px;
            width: 100%;
            transition: border-color 0.15s;
            outline: none;
        }
        .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
        .form-input::placeholder { color: var(--text-muted); }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Issue list item */
        .issue-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-subtle);
            transition: background 0.1s;
            text-decoration: none;
        }

        .issue-row:last-child { border-bottom: none; }
        .issue-row:hover { background: var(--bg-card-hover); }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #6ee7b7; }
        .alert-error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.25);  color: #fca5a5; }

        /* Select */
        select.form-input { cursor: pointer; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    </style>
</head>
<body>


<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">IF</div>
        <span class="logo-text">ISSUEFLOW</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Navigation</div>

        <a href="<?php echo e(route('issues.index')); ?>" class="nav-item <?php echo e(request()->routeIs('issues.index') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            All Issues
        </a>

        <a href="<?php echo e(route('issues.create')); ?>" class="nav-item <?php echo e(request()->routeIs('issues.create') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Submit Issue
        </a>

        <?php if(auth()->user()->hasElevatedAccess()): ?>
        <a href="<?php echo e(route('issues.escalated')); ?>" class="nav-item <?php echo e(request()->routeIs('issues.escalated') ? 'active' : ''); ?>">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Escalated
            <?php $esc = \App\Models\Issue::escalated()->count() ?>
            <?php if($esc > 0): ?>
            <span style="background:rgba(239,68,68,0.2);color:#fca5a5;font-size:10px;padding:1px 6px;border-radius:99px;margin-left:auto;font-weight:700;"><?php echo e($esc); ?></span>
            <?php endif; ?>
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo e(auth()->user()->name); ?></div>
                <div class="user-role"><?php echo e(auth()->user()->role->label()); ?></div>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin-top:12px">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-secondary" style="width:100%;justify-content:center;font-size:12px;padding:6px 12px;">
                Sign out
            </button>
        </form>
    </div>
</aside>


<main class="main">
    <div class="topbar">
        <span class="page-title"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></span>
        <?php echo $__env->yieldContent('topbar-actions'); ?>
    </div>

    <div class="content">
        <?php if(session('success')): ?>
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>
</main>

</body>
</html><?php /**PATH /Users/markferrer/Documents/projects/issue_tracker/resources/views/app.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — IssueFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0a0b0f;
            color: #e8eaf0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            background-image: linear-gradient(rgba(99,102,241,0.04) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(99,102,241,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
        }

        .bg-glow {
            position: fixed;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -60%);
            pointer-events: none;
        }

        .login-wrap {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 380px;
            padding: 16px;
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-mark {
            width: 48px; height: 48px;
            background: #6366f1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Mono', monospace;
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin: 0 auto 16px;
        }

        .brand-name {
            font-family: 'Space Mono', monospace;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .brand-sub {
            font-size: 13px;
            color: #9096a8;
            margin-top: 4px;
        }

        .card {
            background: #111318;
            border: 1px solid #252830;
            border-radius: 12px;
            padding: 28px;
        }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #9096a8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .form-input {
            background: #1a1d26;
            border: 1px solid #252830;
            border-radius: 8px;
            color: #e8eaf0;
            font-size: 14px;
            font-family: inherit;
            padding: 10px 14px;
            width: 100%;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }

        .error-msg {
            font-size: 12px;
            color: #fca5a5;
            margin-top: 4px;
        }

        .btn-login {
            width: 100%;
            padding: 11px;
            background: #6366f1;
            color: white;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            margin-top: 8px;
        }
        .btn-login:hover { background: #4f46e5; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        .demo-accounts {
            margin-top: 24px;
            padding: 16px;
            background: rgba(99,102,241,0.06);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 8px;
        }

        .demo-title {
            font-size: 11px;
            font-weight: 600;
            color: #9096a8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            font-family: 'Space Mono', monospace;
        }

        .demo-row {
            display: grid;
            grid-template-columns: 1fr 1.4fr 0.8fr;
            font-size: 12px;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            gap: 8px;
        }
        .demo-row:last-child { border-bottom: none; }
        .demo-role { color: #818cf8; font-weight: 500; }
        .demo-email { color: #9096a8; font-family: 'Space Mono', monospace; font-size: 11px; }
        .demo-pass { color: #555c70; font-family: 'Space Mono', monospace; font-size: 11px; }

        .alert {
            padding: 10px 14px;
            border-radius: 7px;
            font-size: 13px;
            margin-bottom: 18px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5;
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="bg-glow"></div>

    <div class="login-wrap">
        <div class="brand">
            <div class="brand-mark">IF</div>
            <div class="brand-name">ISSUEFLOW</div>
            <div class="brand-sub">Issue Intake &amp; Smart Summary System</div>
        </div>

        <div class="card">
            <?php if($errors->any()): ?>
            <div class="alert">
                <?php echo e($errors->first()); ?>

            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="<?php echo e(old('email')); ?>"
                        placeholder="you@example.com"
                        required
                        autofocus
                    >
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error-msg"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>

        <div class="demo-accounts">
            <div class="demo-title">// Demo Accounts</div>
            <div class="demo-row">
                <span class="demo-role">Admin</span>
                <span class="demo-email">admin@example.com</span>
                <span class="demo-pass">password</span>
            </div>
            <div class="demo-row">
                <span class="demo-role">Moderator</span>
                <span class="demo-email">moderator@…com</span>
                <span class="demo-pass">password</span>
            </div>
            <div class="demo-row">
                <span class="demo-role">User</span>
                <span class="demo-email">user@example.com</span>
                <span class="demo-pass">password</span>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH /Users/markferrer/Documents/projects/issue_tracker/resources/views/login.blade.php ENDPATH**/ ?>
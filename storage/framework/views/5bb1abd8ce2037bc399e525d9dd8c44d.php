<?php $__env->startSection('title', 'Submit Issue'); ?>
<?php $__env->startSection('page-title', 'Submit New Issue'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('issues.index')); ?>" class="btn btn-secondary">← Cancel</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div style="max-width:680px;">
    <div class="card" style="padding:28px;">

        <?php if($errors->any()): ?>
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:8px;padding:14px 16px;margin-bottom:24px;">
            <div style="font-size:13px;font-weight:600;color:#fca5a5;margin-bottom:8px;">Please fix the following errors:</div>
            <ul style="margin:0;padding-left:16px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li style="font-size:13px;color:#fca5a5;margin-bottom:3px;"><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('issues.store')); ?>">
            <?php echo csrf_field(); ?>

            <div style="margin-bottom:20px;">
                <label class="form-label" for="title">Issue Title <span style="color:#ef4444;">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-input"
                    value="<?php echo e(old('title')); ?>"
                    placeholder="Brief, descriptive title of the issue"
                    required
                >
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="font-size:12px;color:#fca5a5;margin-top:5px;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div>
                    <label class="form-label" for="priority">Priority <span style="color:#ef4444;">*</span></label>
                    <select id="priority" name="priority" class="form-input" required>
                        <option value="">Select priority...</option>
                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->value); ?>" <?php echo e(old('priority') === $p->value ? 'selected' : ''); ?>>
                                <?php echo e($p->label()); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="font-size:12px;color:#fca5a5;margin-top:5px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="form-label" for="category">Category <span style="color:#ef4444;">*</span></label>
                    <select id="category" name="category" class="form-input" required>
                        <option value="">Select category...</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->value); ?>" <?php echo e(old('category') === $c->value ? 'selected' : ''); ?>>
                                <?php echo e($c->icon()); ?> <?php echo e($c->label()); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="font-size:12px;color:#fca5a5;margin-top:5px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                ><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="font-size:12px;color:#fca5a5;margin-top:5px;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div style="background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.15);border-radius:8px;padding:12px 16px;margin-bottom:24px;display:flex;gap:10px;align-items:flex-start;">
                <span style="background:#6366f1;color:white;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;padding:2px 6px;border-radius:3px;flex-shrink:0;margin-top:1px;">AI</span>
                <span style="font-size:12px;color:#9096a8;line-height:1.5;">An AI summary and suggested next action will be automatically generated from your description when you submit.</span>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    Submit Issue
                </button>
                <a href="<?php echo e(route('issues.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/markferrer/Documents/projects/issue_tracker/resources/views/issues/create.blade.php ENDPATH**/ ?>
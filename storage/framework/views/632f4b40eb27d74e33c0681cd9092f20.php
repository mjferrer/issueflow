<?php $__env->startSection('title', '#' . $issue->id . ' — ' . $issue->title); ?>
<?php $__env->startSection('page-title', '#' . $issue->id); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('issues.index')); ?>" class="btn btn-secondary">
        ← Back
    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $issue)): ?>
    <a href="<?php echo e(route('issues.edit', $issue)); ?>" class="btn btn-secondary">
        Edit Issue
    </a>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $issue)): ?>
    <form method="POST" action="<?php echo e(route('issues.destroy', $issue)); ?>" onsubmit="return confirm('Delete this issue permanently?')">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">


<div>
    
    <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
            <span class="badge badge-<?php echo e($issue->priority->value); ?>" style="font-size:12px;padding:3px 10px;">
                <?php echo e($issue->priority->label()); ?>

            </span>
            <span class="badge badge-<?php echo e($issue->status->value); ?>" style="font-size:12px;padding:3px 10px;">
                <?php echo e($issue->status->label()); ?>

            </span>
            <span style="font-size:12px;color:var(--text-muted);"><?php echo e($issue->category->icon()); ?> <?php echo e($issue->category->label()); ?></span>
            <?php if($issue->is_escalated): ?>
            <span class="badge badge-escalated" style="font-size:12px;padding:3px 10px;">⚠ ESCALATED</span>
            <?php endif; ?>
        </div>

        <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);margin-bottom:8px;font-family:'Space Mono',monospace;line-height:1.3;">
            <?php echo e($issue->title); ?>

        </h1>

        <div style="font-size:12px;color:var(--text-muted);display:flex;gap:16px;flex-wrap:wrap;">
            <span>Submitted by <strong style="color:var(--text-secondary);"><?php echo e($issue->user->name); ?></strong></span>
            <span><?php echo e($issue->created_at->format('M j, Y \a\t g:ia')); ?></span>
            <?php if($issue->resolved_at): ?>
            <span>Resolved <?php echo e($issue->resolved_at->diffForHumans()); ?></span>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($issue->is_escalated): ?>
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.3);border-radius:10px;padding:16px 20px;margin-bottom:16px;display:flex;gap:12px;align-items:flex-start;">
        <svg width="18" height="18" style="color:#ef4444;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <div style="font-size:13px;font-weight:600;color:#fca5a5;margin-bottom:3px;">Escalation Required</div>
            <div style="font-size:12px;color:#9096a8;"><?php echo e($issue->escalation_reason); ?></div>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($issue->ai_summary): ?>
    <div style="background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.2);border-radius:10px;padding:20px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="background:#6366f1;color:white;font-size:10px;font-weight:700;font-family:'Space Mono',monospace;padding:2px 8px;border-radius:4px;">AI</span>
                <span style="font-size:12px;font-weight:600;color:#818cf8;text-transform:uppercase;letter-spacing:.06em;">Smart Summary</span>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('regenerateSummary', $issue)): ?>
            <form method="POST" action="<?php echo e(route('issues.regenerate-summary', $issue)); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-secondary btn-sm" style="font-size:11px;">
                    ↻ Regenerate
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div style="margin-bottom:14px;">
            <div style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Summary</div>
            <div style="font-size:13px;color:var(--text-secondary);line-height:1.6;"><?php echo e($issue->ai_summary); ?></div>
        </div>

        <?php if($issue->ai_next_action): ?>
        <div>
            <div style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Suggested Next Action</div>
            <div style="font-size:13px;color:#a5b4fc;line-height:1.6;display:flex;gap:8px;align-items:flex-start;">
                <span style="flex-shrink:0;margin-top:2px;">→</span>
                <?php echo e($issue->ai_next_action); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;">Description</div>
        <div style="font-size:14px;color:var(--text-secondary);line-height:1.7;white-space:pre-wrap;"><?php echo e($issue->description); ?></div>
    </div>

    
    <?php if($issue->statusChanges->count() > 0): ?>
    <div class="card" style="padding:24px;">
        <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Status History</div>
        <div style="display:flex;flex-direction:column;gap:0;">
            <?php $__currentLoopData = $issue->statusChanges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="display:flex;gap:14px;padding:10px 0;border-bottom:1px solid var(--border-subtle);">
                <div style="flex-shrink:0;width:8px;height:8px;border-radius:50%;background:var(--accent);margin-top:4px;"></div>
                <div style="flex:1;">
                    <div style="font-size:12px;color:var(--text-secondary);margin-bottom:2px;">
                        <?php if($change->from_status): ?>
                            <span class="badge badge-<?php echo e($change->from_status->value); ?>" style="font-size:10px;padding:1px 6px;"><?php echo e($change->from_status->label()); ?></span>
                            <span style="color:var(--text-muted);margin:0 4px;">→</span>
                        <?php endif; ?>
                        <span class="badge badge-<?php echo e($change->to_status->value); ?>" style="font-size:10px;padding:1px 6px;"><?php echo e($change->to_status->label()); ?></span>
                    </div>
                    <?php if($change->note): ?>
                    <div style="font-size:12px;color:var(--text-muted);"><?php echo e($change->note); ?></div>
                    <?php endif; ?>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                        by <?php echo e($change->user?->name ?? 'System'); ?> · <?php echo e($change->changed_at?->diffForHumans()); ?>

                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>


<div>
    
    <div class="card" style="padding:20px;margin-bottom:16px;">
        <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Details</div>

        <div style="display:flex;flex-direction:column;gap:12px;">
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px;">Priority</div>
                <span class="badge badge-<?php echo e($issue->priority->value); ?>"><?php echo e($issue->priority->label()); ?></span>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px;">Status</div>
                <span class="badge badge-<?php echo e($issue->status->value); ?>"><?php echo e($issue->status->label()); ?></span>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px;">Category</div>
                <span style="font-size:13px;color:var(--text-secondary);"><?php echo e($issue->category->icon()); ?> <?php echo e($issue->category->label()); ?></span>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px;">Submitted by</div>
                <span style="font-size:13px;color:var(--text-secondary);"><?php echo e($issue->user->name); ?></span>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px;">Created</div>
                <span style="font-size:13px;color:var(--text-secondary);"><?php echo e($issue->created_at->format('M j, Y')); ?></span>
            </div>
            <?php if($issue->resolved_at): ?>
            <div>
                <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px;">Resolved</div>
                <span style="font-size:13px;color:#6ee7b7;"><?php echo e($issue->resolved_at->format('M j, Y')); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('changeStatus', $issue)): ?>
    <div class="card" style="padding:20px;">
        <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Update Status</div>
        <form method="POST" action="<?php echo e(route('issues.update', $issue)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
            <div style="margin-bottom:10px;">
                <select name="status" class="form-input">
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->value); ?>" <?php echo e($issue->status === $s ? 'selected' : ''); ?>><?php echo e($s->label()); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div style="margin-bottom:10px;">
                <textarea name="note" class="form-input" rows="2" placeholder="Optional note..." style="resize:vertical;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                Update Status
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/markferrer/Documents/projects/issue_tracker/resources/views/issues/show.blade.php ENDPATH**/ ?>
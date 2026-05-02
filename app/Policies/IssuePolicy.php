<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    /**
     * Admin and moderators can view all issues.
     * Regular users can only view their own.
     */
    public function view(User $user, Issue $issue): bool
    {
        if ($user->hasElevatedAccess()) {
            return true;
        }

        return $issue->user_id === $user->id;
    }

    /**
     * Any authenticated user can create issues.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Admin and moderators can update any issue.
     * Regular users can only update their own open issues.
     */
    public function update(User $user, Issue $issue): bool
    {
        if ($user->hasElevatedAccess()) {
            return true;
        }

        return $issue->user_id === $user->id;
    }

    /**
     * Only admins can delete issues.
     */
    public function delete(User $user, Issue $issue): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins and moderators can change issue status.
     */
    public function changeStatus(User $user, Issue $issue): bool
    {
        return $user->hasElevatedAccess();
    }

    /**
     * Only admins and moderators can regenerate AI summaries.
     */
    public function regenerateSummary(User $user, Issue $issue): bool
    {
        return $user->hasElevatedAccess();
    }

    /**
     * Only admins and moderators can view escalated issues dashboard.
     */
    public function viewEscalated(User $user): bool
    {
        return $user->hasElevatedAccess();
    }
}
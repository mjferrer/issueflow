<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Issue;
use App\Models\IssueStatusChange;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class IssueService
{
    public function __construct(private readonly AIService $aiService) {}

    /**
     * Create a new issue, generate AI summary, and evaluate escalation.
     */
    public function create(array $validated, User $user): Issue
    {
        // Generate AI summary before saving
        $aiResult = $this->aiService->generateFromFields(
            title:       $validated['title'],
            description: $validated['description'],
            priority:    $validated['priority'],
            category:    $validated['category'],
        );

        $issue = Issue::create([
            ...$validated,
            'user_id'       => $user->id,
            'ai_summary'    => $aiResult['summary'],
            'ai_next_action' => $aiResult['next_action'],
        ]);

        // Evaluate and apply escalation rules
        $issue->refreshEscalation();
        $issue->refresh();

        // Log initial status
        IssueStatusChange::create([
            'issue_id'   => $issue->id,
            'user_id'    => $user->id,
            'from_status' => null,
            'to_status'  => $issue->status,
            'note'       => 'Issue created.',
            'changed_at' => now(),
        ]);

        return $issue;
    }

    /**
     * Update an issue, track status changes, and re-evaluate escalation.
     */
    public function update(Issue $issue, array $validated, User $user): Issue
    {
        $previousStatus = $issue->status;

        $issue->update($validated);

        // Track status change if it changed
        if (isset($validated['status']) && $previousStatus !== $issue->status) {
            IssueStatusChange::create([
                'issue_id'    => $issue->id,
                'user_id'     => $user->id,
                'from_status' => $previousStatus,
                'to_status'   => $issue->status,
                'note'        => $validated['note'] ?? null,
                'changed_at'  => now(),
            ]);

            // Mark resolved_at when closed/resolved
            if (in_array($issue->status, [Status::Resolved, Status::Closed])) {
                $issue->update(['resolved_at' => now()]);
            } elseif ($issue->status->isOpen()) {
                $issue->update(['resolved_at' => null]);
            }
        }

        // Re-evaluate escalation whenever anything changes
        $issue->refreshEscalation();
        $issue->refresh();

        return $issue;
    }

    /**
     * Regenerate AI summary for an existing issue.
     */
    public function regenerateSummary(Issue $issue): Issue
    {
        $aiResult = $this->aiService->generateSummaryForIssue($issue);

        $issue->update([
            'ai_summary'     => $aiResult['summary'],
            'ai_next_action' => $aiResult['next_action'],
        ]);

        return $issue->refresh();
    }

    /**
     * List issues with optional filters and pagination.
     */
    public function list(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Issue::with('user')
            ->forUser($user)
            ->byStatus($filters['status'] ?? null)
            ->byPriority($filters['priority'] ?? null)
            ->byCategory($filters['category'] ?? null)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all escalated issues (admin/moderator only).
     */
    public function escalated(int $perPage = 15): LengthAwarePaginator
    {
        return Issue::with('user')
            ->escalated()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Refresh escalation flags for all open issues (scheduled command).
     */
    public function refreshAllEscalations(): int
    {
        $count = 0;

        Issue::with('user')
            ->whereIn('status', ['open', 'in_progress', 'on_hold'])
            ->chunk(100, function ($issues) use (&$count) {
                foreach ($issues as $issue) {
                    $issue->refreshEscalation();
                    $count++;
                }
            });

        return $count;
    }
}
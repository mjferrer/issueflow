<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Status;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Services\IssueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class IssueController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private readonly IssueService $issueService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'priority', 'category']);
        $issues  = $this->issueService->list(auth()->user(), $filters, 12);

        $escalatedCount = auth()->user()->hasElevatedAccess()
            ? Issue::escalated()->count()
            : 0;

        return view('issues.index', [
            'issues'         => $issues,
            'filters'        => $filters,
            'escalatedCount' => $escalatedCount,
            'statuses'       => Status::cases(),
            'priorities'     => Priority::cases(),
            'categories'     => Category::cases(),
        ]);
    }

    public function create(): View
    {
        return view('issues.create', [
            'priorities' => Priority::cases(),
            'categories' => Category::cases(),
            'statuses'   => Status::cases(),
        ]);
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $issue = $this->issueService->create($request->validated(), $request->user());

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue submitted successfully! AI summary has been generated.');
    }

    public function show(Issue $issue): View
    {
        $this->authorize('view', $issue);

        $issue->load(['user', 'statusChanges.user']);

        return view('issues.show', [
            'issue'      => $issue,
            'statuses'   => Status::cases(),
            'priorities' => Priority::cases(),
        ]);
    }

    public function edit(Issue $issue): View
    {
        $this->authorize('update', $issue);

        return view('issues.edit', [
            'issue'      => $issue,
            'priorities' => Priority::cases(),
            'categories' => Category::cases(),
            'statuses'   => Status::cases(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $this->issueService->update($issue, $request->validated(), $request->user());

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue updated successfully.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $this->authorize('delete', $issue);

        $issue->delete();

        return redirect()
            ->route('issues.index')
            ->with('success', 'Issue deleted successfully.');
    }

    public function escalated(Request $request): View
    {
        $this->authorize('viewEscalated', Issue::class);

        $issues = $this->issueService->escalated(12);

        return view('issues.escalated', [
            'issues' => $issues,
        ]);
    }

    public function regenerateSummary(Issue $issue): RedirectResponse
    {
        $this->authorize('regenerateSummary', $issue);

        $this->issueService->regenerateSummary($issue);

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'AI summary regenerated.');
    }
}
<?php

namespace App\Services;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Issue;
use Exception;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Generate a short summary and suggested next action for an issue.
     * Uses OpenAI if configured; falls back to rules-based engine.
     *
     * @return array{summary: string, next_action: string}
     */
    public function generateSummaryForIssue(Issue $issue): array
    {
        if ($this->isOpenAIAvailable()) {
            try {
                return $this->generateWithOpenAI($issue->title, $issue->description, $issue->priority->value, $issue->category->value);
            } catch (Exception $e) {
                Log::warning('OpenAI failed, falling back to rules engine.', [
                    'error'    => $e->getMessage(),
                    'issue_id' => $issue->id,
                ]);
            }
        }

        return $this->generateWithRulesEngine($issue);
    }

    /**
     * Generate from raw fields (used before the model is saved).
     *
     * @return array{summary: string, next_action: string}
     */
    public function generateFromFields(string $title, string $description, string $priority, string $category): array
    {
        if ($this->isOpenAIAvailable()) {
            try {
                return $this->generateWithOpenAI($title, $description, $priority, $category);
            } catch (Exception $e) {
                Log::warning('OpenAI failed, falling back to rules engine.', ['error' => $e->getMessage()]);
            }
        }

        return $this->generateWithRulesEngineFromFields($title, $description, $priority, $category);
    }

    // ─── OpenAI Integration ───────────────────────────────────────────────────

    private function isOpenAIAvailable(): bool
    {
        return !empty(config('services.openai.key'));
    }

    /**
     * @return array{summary: string, next_action: string}
     * @throws Exception
     */
    private function generateWithOpenAI(string $title, string $description, string $priority, string $category): array
    {
        $client = \OpenAI::client(config('services.openai.key'));

        $prompt = <<<PROMPT
You are a support operations assistant. Analyze this support issue and return a JSON object only.

Issue Title: {$title}
Description: {$description}
Priority: {$priority}
Category: {$category}

Return ONLY a valid JSON object (no markdown, no explanation) with exactly these two fields:
{
  "summary": "A concise 1-2 sentence summary of the core problem (max 200 chars)",
  "next_action": "A specific, actionable next step for the support team (max 150 chars)"
}
PROMPT;

        $response = $client->chat()->create([
            'model'       => config('services.openai.model', 'gpt-4o-mini'),
            'max_tokens'  => 250,
            'temperature' => 0.3,
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a concise support triage assistant. Always respond with valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $content = $response->choices[0]->message->content ?? '';
        $content = trim(preg_replace('/^```json|```$/m', '', $content));

        $data = json_decode($content, true);

        if (!isset($data['summary'], $data['next_action'])) {
            throw new Exception('Invalid JSON response from OpenAI: ' . $content);
        }

        return [
            'summary'     => substr($data['summary'], 0, 250),
            'next_action' => substr($data['next_action'], 0, 200),
        ];
    }

    // ─── Rules-Based Fallback ─────────────────────────────────────────────────

    private function generateWithRulesEngine(Issue $issue): array
    {
        return $this->generateWithRulesEngineFromFields(
            $issue->title,
            $issue->description,
            $issue->priority->value,
            $issue->category->value,
        );
    }

    /**
     * @return array{summary: string, next_action: string}
     */
    private function generateWithRulesEngineFromFields(string $title, string $description, string $priority, string $category): array
    {
        $summary    = $this->buildSummary($title, $description);
        $nextAction = $this->determineNextAction($priority, $category, $description);

        return [
            'summary'     => $summary,
            'next_action' => $nextAction,
        ];
    }

    private function buildSummary(string $title, string $description): string
    {
        $descriptionSnippet = strlen($description) > 120
            ? substr($description, 0, 117) . '...'
            : $description;

        return "{$title} — {$descriptionSnippet}";
    }

    private function determineNextAction(string $priority, string $category, string $description): string
    {
        // Category-specific rules (highest specificity)
        if ($category === Category::Security->value) {
            return 'Escalate to the security team immediately and notify the CISO.';
        }

        if ($category === Category::Infrastructure->value && $priority === Priority::Critical->value) {
            return 'Page the on-call infrastructure engineer and open an incident channel.';
        }

        if ($category === Category::DataIssue->value) {
            return 'Assign to data engineering; identify scope of affected records before proceeding.';
        }

        // Priority-based rules
        if ($priority === Priority::Critical->value) {
            return 'Escalate immediately — assign to a senior engineer and notify the team lead.';
        }

        if ($priority === Priority::High->value) {
            if ($category === Category::Bug->value) {
                return 'Reproduce the bug in staging, assign to a senior developer for same-day resolution.';
            }
            return 'Assign to an available engineer; target resolution within 24 hours.';
        }

        // Category defaults
        $actionMap = [
            Category::Bug->value         => 'Log reproduction steps and assign to the relevant engineering team.',
            Category::Feature->value     => 'Add to the product backlog and schedule for next sprint review.',
            Category::Performance->value => 'Profile the affected component and identify the bottleneck.',
            Category::UX->value          => 'Share with the design team for review in the next UX sprint.',
            Category::Other->value       => 'Triage with the team lead to assign the appropriate owner.',
        ];

        $action = $actionMap[$category] ?? 'Review and assign to the appropriate team member.';

        // Keyword-based overrides in description
        if (stripos($description, 'crash') !== false || stripos($description, 'down') !== false) {
            $action = 'Investigate service health immediately; check logs and error monitoring dashboard.';
        }

        if (stripos($description, 'data loss') !== false || stripos($description, 'deleted') !== false) {
            $action = 'Halt related operations, engage DBA team, and initiate data recovery procedures.';
        }

        if (stripos($description, 'customer') !== false && $priority !== Priority::Low->value) {
            $action = 'Prioritize — customer-facing issue. Assign to senior support and send acknowledgment.';
        }

        return $action;
    }
}
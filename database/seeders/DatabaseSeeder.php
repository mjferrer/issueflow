<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Issue;
use App\Models\IssueStatusChange;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding users...');
        $this->seedUsers();

        $this->command->info('Seeding issues...');
        $this->seedIssues();

        $this->command->info('✅ Database seeded successfully.');
        $this->command->table(['Role', 'Email', 'Password'], [
            ['Admin',     'admin@example.com',     'password'],
            ['Moderator', 'moderator@example.com', 'password'],
            ['User',      'user@example.com',      'password'],
            ['User',      'jane@example.com',      'password'],
            ['User',      'bob@example.com',       'password'],
        ]);
    }

    // ─── Users ────────────────────────────────────────────────────────────────

    private function seedUsers(): void
    {
        // Admin
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name'     => 'Alex Admin',
            'password' => Hash::make('password'),
            'role'     => Role::Admin,
        ]);

        // Moderator
        User::updateOrCreate(['email' => 'moderator@example.com'], [
            'name'     => 'Morgan Moderator',
            'password' => Hash::make('password'),
            'role'     => Role::Moderator,
        ]);

        // Regular users
        User::updateOrCreate(['email' => 'user@example.com'], [
            'name'     => 'Sam User',
            'password' => Hash::make('password'),
            'role'     => Role::User,
        ]);

        User::updateOrCreate(['email' => 'jane@example.com'], [
            'name'     => 'Jane Doe',
            'password' => Hash::make('password'),
            'role'     => Role::User,
        ]);

        User::updateOrCreate(['email' => 'bob@example.com'], [
            'name'     => 'Bob Smith',
            'password' => Hash::make('password'),
            'role'     => Role::User,
        ]);
    }

    // ─── Issues ───────────────────────────────────────────────────────────────

    private function seedIssues(): void
    {
        $admin     = User::where('email', 'admin@example.com')->first();
        $moderator = User::where('email', 'moderator@example.com')->first();
        $sam       = User::where('email', 'user@example.com')->first();
        $jane      = User::where('email', 'jane@example.com')->first();
        $bob       = User::where('email', 'bob@example.com')->first();

        $issueData = [
            // ── Critical / Escalated ──────────────────────────────────────────
            [
                'user'        => $sam,
                'title'       => 'Production database is unresponsive',
                'description' => 'The main production MySQL database is returning connection timeout errors. All services dependent on it are down, including user authentication and order processing. Started approximately 14 minutes ago after the routine index rebuild job ran.',
                'priority'    => Priority::Critical,
                'category'    => Category::Infrastructure,
                'status'      => Status::InProgress,
                'ai_summary'  => 'Production MySQL database is timing out after a routine index rebuild, bringing down auth and order processing.',
                'ai_next_action' => 'Page on-call DBA immediately, roll back index rebuild, failover to read replica if available.',
                'is_escalated' => true,
                'escalation_reason' => 'Critical priority issue requires immediate attention.',
                'created_at'  => now()->subHours(2),
            ],
            [
                'user'        => $jane,
                'title'       => 'SQL injection vulnerability found in search endpoint',
                'description' => 'During a routine security audit, we discovered that the /api/v2/search endpoint does not properly sanitize the "query" parameter. An attacker could extract user data or modify records. Confirmed exploitable in staging environment.',
                'priority'    => Priority::Critical,
                'category'    => Category::Security,
                'status'      => Status::Open,
                'ai_summary'  => 'Confirmed SQL injection in the /api/v2/search endpoint allows potential data extraction and modification.',
                'ai_next_action' => 'Disable the endpoint immediately, patch input sanitization, perform security audit of similar endpoints.',
                'is_escalated' => true,
                'escalation_reason' => 'Security issues must be escalated regardless of priority.',
                'created_at'  => now()->subHours(5),
            ],
            // ── High Priority ─────────────────────────────────────────────────
            [
                'user'        => $bob,
                'title'       => 'Checkout process failing for users with saved cards',
                'description' => 'Multiple customers have reported that clicking "Pay Now" with a saved credit card results in an infinite spinner. The error in Sentry shows a 500 from the payment processor gateway. New card entries seem to work fine. Affecting approximately 30% of checkout attempts.',
                'priority'    => Priority::High,
                'category'    => Category::Bug,
                'status'      => Status::InProgress,
                'ai_summary'  => 'Saved card checkout fails with a 500 error from the payment gateway, affecting ~30% of transactions.',
                'ai_next_action' => 'Check payment gateway API logs, test with different saved cards, roll back last payment service deploy if applicable.',
                'is_escalated' => false,
                'created_at'  => now()->subHours(8),
            ],
            [
                'user'        => $sam,
                'title'       => 'Dashboard load time exceeds 12 seconds for enterprise accounts',
                'description' => 'Enterprise customers with more than 10,000 records are experiencing dashboard load times of 12-18 seconds. This is causing significant complaints and at least two enterprise clients have threatened to cancel. The issue started after the v2.4.0 release last Tuesday.',
                'priority'    => Priority::High,
                'category'    => Category::Performance,
                'status'      => Status::Open,
                'ai_summary'  => 'Dashboard loads 12-18s for enterprise accounts after v2.4.0, causing client churn risk.',
                'ai_next_action' => 'Profile dashboard queries for large account datasets, add pagination or lazy loading, revert v2.4.0 changes if needed.',
                'is_escalated' => true,
                'escalation_reason' => 'High-priority issue has been open for 72 hours without resolution.',
                'created_at'  => now()->subHours(72),
            ],
            // ── Medium Priority ───────────────────────────────────────────────
            [
                'user'        => $jane,
                'title'       => 'Email verification link expires too quickly',
                'description' => 'New users report that by the time they check their email (sometimes within 5-10 minutes), the verification link has already expired. The current TTL is set to 5 minutes. The standard industry practice is 24-72 hours for initial verification.',
                'priority'    => Priority::Medium,
                'category'    => Category::Bug,
                'status'      => Status::Open,
                'ai_summary'  => 'Email verification links expire in 5 minutes, far too short, causing new user registration failures.',
                'ai_next_action' => 'Update VERIFICATION_TTL to 24h in config, redeploy, and notify affected users to re-register.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(1),
            ],
            [
                'user'        => $bob,
                'title'       => 'Add bulk export to CSV for reports module',
                'description' => 'The reports module currently only supports downloading individual records as PDF. Multiple teams have requested a way to export all filtered results as a CSV for further analysis in Excel or Google Sheets. This was discussed in the Q3 roadmap meeting.',
                'priority'    => Priority::Medium,
                'category'    => Category::Feature,
                'status'      => Status::Open,
                'ai_summary'  => 'Multiple teams request bulk CSV export from the reports module, currently only PDF per-record is available.',
                'ai_next_action' => 'Add to sprint backlog, design API endpoint for export with streaming support for large datasets.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(2),
            ],
            [
                'user'        => $sam,
                'title'       => 'Dark mode colors are inaccessible in settings panel',
                'description' => 'In dark mode, the settings panel uses a gray-on-gray color scheme that fails WCAG 2.1 AA contrast requirements. Specifically the form labels (#888 on #777) have a contrast ratio of 1.2:1 when the minimum required is 4.5:1. Users with visual impairments have reported this via accessibility feedback.',
                'priority'    => Priority::Medium,
                'category'    => Category::UX,
                'status'      => Status::OnHold,
                'ai_summary'  => 'Dark mode settings panel fails WCAG AA contrast with 1.2:1 ratio — inaccessible for visually impaired users.',
                'ai_next_action' => 'Update CSS color tokens for dark mode, ensure 4.5:1 contrast minimum, run automated accessibility audit.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(3),
            ],
            [
                'user'        => $jane,
                'title'       => 'Wrong totals shown on invoices for multi-currency orders',
                'description' => 'Orders placed in currencies other than USD show incorrect totals on the generated invoice PDFs. The line items are correct but the subtotal and tax calculations appear to use the wrong exchange rate. Confirmed on EUR, GBP, and JPY orders.',
                'priority'    => Priority::High,
                'category'    => Category::DataIssue,
                'status'      => Status::Open,
                'ai_summary'  => 'Invoice PDFs show incorrect subtotals and tax for multi-currency orders due to wrong exchange rate in calculation.',
                'ai_next_action' => 'Audit invoice generation service, fix currency conversion logic, and regenerate affected invoices.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(1),
            ],
            // ── Low Priority ──────────────────────────────────────────────────
            [
                'user'        => $bob,
                'title'       => 'Typo in onboarding welcome email subject line',
                'description' => 'The welcome email sent to new users has "Welcme to Acme Corp!" in the subject line instead of "Welcome to Acme Corp!". Minor issue but looks unprofessional to new users.',
                'priority'    => Priority::Low,
                'category'    => Category::Other,
                'status'      => Status::Open,
                'ai_summary'  => 'Typo in welcome email subject line: "Welcme" instead of "Welcome" — affects first impression for new users.',
                'ai_next_action' => 'Fix subject line template in email configuration and redeploy email service.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(4),
            ],
            [
                'user'        => $sam,
                'title'       => 'Add keyboard shortcut to create new issue',
                'description' => 'Power users would benefit from a keyboard shortcut (e.g. Ctrl+N or N) to quickly navigate to the new issue form without using the mouse. This is a common pattern in Jira, GitHub, and Linear.',
                'priority'    => Priority::Low,
                'category'    => Category::Feature,
                'status'      => Status::Open,
                'ai_summary'  => 'Feature request: keyboard shortcut to create new issues, consistent with tools like Jira and Linear.',
                'ai_next_action' => 'Add to UX backlog; implement with a global keydown handler and a shortcut guide modal.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(5),
            ],
            // ── Resolved ─────────────────────────────────────────────────────
            [
                'user'        => $jane,
                'title'       => 'Login page 500 error after password reset',
                'description' => 'After completing a password reset, users were redirected to the login page which threw a 500 error due to a stale session cookie. The issue was intermittent and affected approximately 15% of password reset flows.',
                'priority'    => Priority::High,
                'category'    => Category::Bug,
                'status'      => Status::Resolved,
                'ai_summary'  => 'Password reset flow caused 500 error on login redirect due to stale session cookie in ~15% of cases.',
                'ai_next_action' => 'Issue resolved — session cookie cleared properly after password reset.',
                'is_escalated' => false,
                'resolved_at' => now()->subHours(6),
                'created_at'  => now()->subDays(2),
            ],
            [
                'user'        => $bob,
                'title'       => 'Notification emails sent in wrong timezone',
                'description' => 'All notification emails were using UTC timestamps instead of the user\'s local timezone. Users in US timezones were seeing events appear 5-8 hours off, causing confusion for scheduled reminders.',
                'priority'    => Priority::Medium,
                'category'    => Category::Bug,
                'status'      => Status::Closed,
                'ai_summary'  => 'Notification emails used UTC instead of user local timezone, causing time confusion for US users.',
                'ai_next_action' => 'Closed — fixed by reading user timezone preference from profile settings during email rendering.',
                'is_escalated' => false,
                'resolved_at' => now()->subDays(1),
                'created_at'  => now()->subDays(4),
            ],
            [
                'user'        => $sam,
                'title'       => 'Image uploads failing for PNG files above 5MB',
                'description' => 'Users uploading profile images or attachments as PNG files larger than 5MB receive a generic error message. JPEGs of the same size work fine. The issue appears to be in the image processing pipeline which doesn\'t handle PNG compression correctly.',
                'priority'    => Priority::Medium,
                'category'    => Category::Bug,
                'status'      => Status::Resolved,
                'ai_summary'  => 'PNG files >5MB fail to upload due to incorrect compression handling in the image processing pipeline.',
                'ai_next_action' => 'Resolved — updated image processor to handle PNG compression; now supports up to 20MB PNG files.',
                'is_escalated' => false,
                'resolved_at' => now()->subHours(12),
                'created_at'  => now()->subDays(3),
            ],
            // ── More variety ─────────────────────────────────────────────────
            [
                'user'        => $jane,
                'title'       => 'API rate limit too aggressive for partner integrations',
                'description' => 'Partner integrations using our API are hitting the 100 req/min rate limit too frequently during sync operations. The partners need to sync large datasets every hour, which requires bursts of up to 500 req/min for short windows.',
                'priority'    => Priority::Medium,
                'category'    => Category::Feature,
                'status'      => Status::InProgress,
                'ai_summary'  => 'Partners hit 100 req/min rate limit during hourly sync bursts — partner tier needs higher limits.',
                'ai_next_action' => 'Create a "partner" API tier with configurable burst limits and implement token bucket algorithm.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(2),
            ],
            [
                'user'        => $bob,
                'title'       => 'Memory leak in background job processor',
                'description' => 'The background job processor (Horizon) shows steadily increasing memory usage over 8-12 hours until it crashes. Memory goes from ~128MB to >2GB before OOM kill. Adding more workers only delays the inevitable. No specific job type has been isolated as the cause yet.',
                'priority'    => Priority::High,
                'category'    => Category::Performance,
                'status'      => Status::InProgress,
                'ai_summary'  => 'Horizon workers leak memory over 8-12 hours from 128MB to 2GB+ causing OOM crashes.',
                'ai_next_action' => 'Profile worker memory with blackfire, check for unclosed DB connections or event listeners, add worker restart schedule.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(1),
            ],
            [
                'user'        => $sam,
                'title'       => 'Two-factor authentication codes not arriving via SMS',
                'description' => 'Users with SMS-based 2FA enabled are not receiving their one-time codes. The issue started after we switched SMS providers last Thursday. Email-based 2FA continues to work normally. Approximately 200 users are locked out of their accounts.',
                'priority'    => Priority::Critical,
                'category'    => Category::Bug,
                'status'      => Status::Open,
                'ai_summary'  => 'SMS 2FA codes stopped delivering after provider switch, locking out ~200 users; email 2FA unaffected.',
                'ai_next_action' => 'Escalate to new SMS provider immediately, revert to old provider or enable email 2FA fallback for all affected users.',
                'is_escalated' => true,
                'escalation_reason' => 'Critical priority issue requires immediate attention.',
                'created_at'  => now()->subHours(18),
            ],
            [
                'user'        => $jane,
                'title'       => 'Improve error messages for form validation failures',
                'description' => 'Current validation error messages are too generic (e.g. "Invalid input"). Users don\'t understand what needs to be corrected. We should provide specific, actionable messages per field like "Email must be a valid email address" or "Password must be at least 8 characters".',
                'priority'    => Priority::Low,
                'category'    => Category::UX,
                'status'      => Status::Open,
                'ai_summary'  => 'Validation errors show generic "Invalid input" messages — users cannot tell which field or what to fix.',
                'ai_next_action' => 'Audit all form validation messages, update to field-specific actionable copy, add inline validation.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(6),
            ],
            [
                'user'        => $bob,
                'title'       => 'Audit log missing entries for admin user deletions',
                'description' => 'The audit log correctly records user creation and modification events, but admin-initiated user deletions are not being logged. This is a compliance requirement under our SOC 2 Type II audit. Discovered during a recent internal review.',
                'priority'    => Priority::High,
                'category'    => Category::Security,
                'status'      => Status::Open,
                'ai_summary'  => 'Admin user deletion events missing from audit log — SOC 2 compliance violation.',
                'ai_next_action' => 'Add delete event listener to User model, backfill missing entries where possible, document gap for auditors.',
                'is_escalated' => true,
                'escalation_reason' => 'Security issues must be escalated regardless of priority.',
                'created_at'  => now()->subDays(2),
            ],
            [
                'user'        => $sam,
                'title'       => 'Mobile app crashes when switching between tabs rapidly',
                'description' => 'On iOS 17 and Android 13, rapidly switching between the Home, Reports, and Settings tabs causes the app to crash with no error message. Reproducible 100% of the time with 3+ rapid taps. Crash reports in Firebase show a NullPointerException in the tab navigation controller.',
                'priority'    => Priority::Medium,
                'category'    => Category::Bug,
                'status'      => Status::Open,
                'ai_summary'  => 'Rapid tab switching crashes mobile app on iOS 17/Android 13 — NullPointerException in nav controller.',
                'ai_next_action' => 'Add navigation guard to debounce tab switches, fix null reference in tab controller initialization.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(3),
            ],
            [
                'user'        => $jane,
                'title'       => 'Add dark mode support to the customer portal',
                'description' => 'The main application has had dark mode since v2.0, but the customer portal (used by clients to view their reports) is still light-mode only. Several enterprise clients have requested dark mode parity, especially for teams that work late shifts.',
                'priority'    => Priority::Low,
                'category'    => Category::Feature,
                'status'      => Status::Open,
                'ai_summary'  => 'Customer portal lacks dark mode while main app supports it — enterprise clients request parity.',
                'ai_next_action' => 'Add to Q4 roadmap, audit portal stylesheets for CSS variable compatibility, implement prefers-color-scheme support.',
                'is_escalated' => false,
                'created_at'  => now()->subDays(7),
            ],
        ];

        foreach ($issueData as $data) {
            $user = $data['user'];
            unset($data['user']);

            $issue = Issue::create([
                ...$data,
                'user_id' => $user->id,
            ]);

            // Add initial status change log
            IssueStatusChange::create([
                'issue_id'    => $issue->id,
                'user_id'     => $user->id,
                'from_status' => null,
                'to_status'   => $issue->status,
                'note'        => 'Issue created.',
                'changed_at'  => $issue->created_at,
            ]);

            // Add a status change history for resolved/closed items
            if (in_array($issue->status, [Status::Resolved, Status::Closed])) {
                IssueStatusChange::create([
                    'issue_id'    => $issue->id,
                    'user_id'     => User::where('email', 'moderator@example.com')->first()->id,
                    'from_status' => Status::InProgress,
                    'to_status'   => $issue->status,
                    'note'        => 'Issue has been resolved and verified.',
                    'changed_at'  => $issue->resolved_at ?? now(),
                ]);
            }
        }
    }
}
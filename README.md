# Issue Intake & Smart Summary System

A full-featured issue tracking system built with **Laravel 11**, **Tailwind CSS**, **Blade**, **Eloquent ORM**, and **OpenAI GPT-4o-mini** for intelligent summaries and next-action suggestions.

---

## Table of Contents

- [Architecture Overview](#architecture-overview)
- [Tech Stack & Decisions](#tech-stack--decisions)
- [Features](#features)
- [Roles & Permissions](#roles--permissions)
- [Setup Instructions](#setup-instructions)
- [API Reference](#api-reference)
- [Sample Data / Seeder](#sample-data--seeder)
- [AI Integration](#ai-integration)
- [What I Would Improve](#what-i-would-improve)

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                        Browser / API Client                  │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP
┌────────────────────────▼────────────────────────────────────┐
│                     Laravel Application                      │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────────────┐  │
│  │   Routes    │→ │  Controllers │→ │  Form Requests     │  │
│  │ web.php     │  │  (thin)      │  │  (validation)      │  │
│  │ api.php     │  └──────┬───────┘  └────────────────────┘  │
│  └─────────────┘         │                                   │
│                   ┌──────▼────────┐                          │
│                   │   Services    │                          │
│                   │  IssueService │                          │
│                   │  AIService    │                          │
│                   └──────┬────────┘                          │
│                   ┌──────▼────────┐                          │
│                   │   Eloquent    │                          │
│                   │   Models      │                          │
│                   └──────┬────────┘                          │
│                   ┌──────▼────────┐                          │
│                   │   SQLite /    │                          │
│                   │   MySQL DB    │                          │
│                   └───────────────┘                          │
│                   ┌───────────────┐                          │
│                   │ OpenAI API    │                          │
│                   │ (with fallback│                          │
│                   │  rules-based) │                          │
│                   └───────────────┘                          │
└─────────────────────────────────────────────────────────────┘
```

### Key Architectural Decisions

**Thin Controllers, Fat Services**: Controllers handle only HTTP concerns (request/response). All business logic lives in `IssueService` and `AIService`. This makes the code testable and maintainable.

**Service Layer Pattern**: `AIService` wraps OpenAI with a rules-based fallback — the system never fails if the API is unavailable or rate-limited.

**Policy-Based Authorization**: Laravel Policies (`IssuePolicy`) centralize permission logic per role, keeping it out of controllers and views.

**Enum Classes**: PHP 8.1 backed enums (`Priority`, `Status`, `Category`) provide type safety and prevent invalid data at the language level.

---

## Tech Stack & Decisions

| Layer | Choice | Reason |
|-------|--------|---------|
| **Backend** | Laravel 11 | Preferred per spec; batteries-included, strong conventions |
| **Database** | MySQL (SQLite for dev) | Relational data fits issues/users/comments perfectly; Eloquent ORM is excellent |
| **Frontend** | Blade + Tailwind CSS v3 | Spec requirement; server-rendered for simplicity and SEO |
| **Auth** | Laravel Breeze (custom) | Thin auth scaffolding, easy to customize |
| **AI** | OpenAI GPT-4o-mini | Fast, cost-efficient, great for short summarization tasks |
| **AI Fallback** | Rules-based engine | System works even without OpenAI key |

**Why MySQL/SQLite?** Issues have structured fields (status, priority, category) that benefit from indexed column queries. Filtering by `status=open AND priority=high` is a natural SQL query. A document DB would complicate these relational lookups.

---

## Features

- ✅ Submit issues with title, description, priority, category, and status
- ✅ View all issues with filtering by status, category, priority
- ✅ AI-generated short summary + suggested next action per issue
- ✅ Rules-based fallback if OpenAI is unavailable
- ✅ Escalation flag for high-priority or overdue issues (>48h open)
- ✅ Three roles: Admin, Moderator, User — with different permissions
- ✅ Full REST API with JSON responses
- ✅ Form validation with clear error messages
- ✅ Audit trail: status change history
- ✅ Beautiful dark-themed UI built with Tailwind CSS

---

## Roles & Permissions

| Action | Admin | Moderator | User |
|--------|-------|-----------|------|
| Create issue | ✅ | ✅ | ✅ |
| View all issues | ✅ | ✅ | Own only |
| Update any issue | ✅ | ✅ | Own only |
| Delete issue | ✅ | ❌ | ❌ |
| Change status | ✅ | ✅ | ❌ |
| Manage users | ✅ | ❌ | ❌ |
| View escalated | ✅ | ✅ | ❌ |
| Regenerate AI summary | ✅ | ✅ | ❌ |

---

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- MySQL 8+ (or use SQLite for quick start)
- OpenAI API key (optional — fallback works without it)

### 1. Clone & Install

```bash
git clone <repo-url> issue-tracker
cd issue-tracker

composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=issue_tracker
DB_USERNAME=root
DB_PASSWORD=your_password

# Or use SQLite for quick testing:
# DB_CONNECTION=sqlite
# (creates database/database.sqlite automatically)

# OpenAI (optional — rules-based fallback used if absent)
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini

# App
APP_URL=http://localhost:8000
```

### 3. Database Setup

```bash
# Create MySQL database first (if using MySQL):
mysql -u root -p -e "CREATE DATABASE issue_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed with sample data (users + 20 issues)
php artisan db:seed
```

### 4. Build Assets & Run

```bash
npm run build
# or for development with hot reload:
npm run dev

# In another terminal:
php artisan serve
```

Visit: http://localhost:8000

### Default Login Credentials (from seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Moderator | moderator@example.com | password |
| User | user@example.com | password |
| User 2 | jane@example.com | password |
| User 3 | bob@example.com | password |

---

## API Reference

All API endpoints are prefixed with `/api/v1/` and require Bearer token authentication.

### Authentication

```bash
# Login
POST /api/v1/login
Content-Type: application/json
{ "email": "admin@example.com", "password": "password" }

# Response
{ "token": "1|abc...", "user": {...} }
```

### Issues

```bash
# List all issues (with optional filters)
GET /api/v1/issues?status=open&priority=high&category=bug&per_page=15

# Create issue
POST /api/v1/issues
Authorization: Bearer {token}
{
  "title": "Login page crashes on mobile",
  "description": "When users tap the login button on iOS 17, the page goes blank.",
  "priority": "high",
  "category": "bug",
  "status": "open"
}

# View single issue
GET /api/v1/issues/{id}
Authorization: Bearer {token}

# Update issue
PATCH /api/v1/issues/{id}
Authorization: Bearer {token}
{ "status": "in_progress", "priority": "critical" }

# Delete issue (admin only)
DELETE /api/v1/issues/{id}
Authorization: Bearer {token}

# Regenerate AI summary (admin/moderator only)
POST /api/v1/issues/{id}/regenerate-summary
Authorization: Bearer {token}

# Get escalated issues (admin/moderator only)
GET /api/v1/issues/escalated
Authorization: Bearer {token}
```

### Validation Errors (422)

```json
{
  "message": "The title field is required.",
  "errors": {
    "title": ["The title field is required."],
    "priority": ["The selected priority is invalid."]
  }
}
```

---

## Sample Data / Seeder

The seeder creates:
- **5 users** across 3 roles
- **20 issues** with varied statuses, priorities, and categories
- **AI summaries** pre-generated (or rules-based if no key)

```bash
# Full reset and reseed:
php artisan migrate:fresh --seed
```

---

## AI Integration

### How It Works

1. When an issue is created or updated, `AIService::generateSummary()` is called
2. It sends the title + description to OpenAI GPT-4o-mini with a structured prompt
3. The model returns a JSON object: `{ "summary": "...", "next_action": "..." }`
4. Both fields are stored in the `issues` table

### Fallback Logic

If OpenAI is unavailable (no key, rate limit, timeout), the system falls back to `RulesEngine::generate()`:

```
IF priority = critical AND status = open → "Escalate immediately to on-call team"
IF category = security → "Assign to security team, notify CISO"  
IF category = bug AND priority >= high → "Reproduce bug, assign to senior dev"
IF status = open AND created > 48h ago → "Flag for manager review — overdue"
... (12 more rules)
```

### Escalation Logic

An issue is flagged for escalation if **any** of:
- Priority is `critical`
- Priority is `high` AND status is `open` for more than 48 hours
- Category is `security` with any open status

Escalated issues appear with a red badge in the UI and are returned by `/api/v1/issues/escalated`.

---

## Running Tests

```bash
php artisan test
# or
./vendor/bin/pest
```

Tests cover:
- Issue CRUD via API
- Validation rejection
- Role-based access control
- Escalation logic
- AI fallback behavior

---

## What I Would Improve With More Time

1. **Queue AI generation** — Move `AIService` calls to a background job (Laravel Queue + Redis) so issue creation doesn't block on the OpenAI API response time.

2. **Webhooks / Notifications** — Email or Slack notifications when issues are escalated or assigned, using Laravel's notification system.

3. **Comments & Activity Log** — A full audit trail with comments, @mentions, and timestamped status changes per issue.

4. **Advanced search** — Full-text search using Laravel Scout + Meilisearch or Typesense for searching across title/description.

5. **Rate limiting** — Per-user API rate limits with `throttle` middleware (basic version is included but not fine-tuned).

6. **File attachments** — Allow screenshots/logs to be attached to issues, stored in S3 via Laravel Storage.

7. **Dashboard analytics** — Charts showing issue volume by category/priority over time using Chart.js.

8. **Two-factor authentication** — Add TOTP 2FA for admin accounts.

9. **AI confidence score** — Return a confidence level with AI summaries so moderators know when to review them.

10. **Caching** — Cache filtered issue lists and escalated counts with Redis for high-traffic scenarios.

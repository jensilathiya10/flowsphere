# FlowSphere

[![tests](https://github.com/jensilathiya10/flowsphere/actions/workflows/tests.yml/badge.svg)](https://github.com/jensilathiya10/flowsphere/actions/workflows/tests.yml)

One workspace to plan, build, and deliver projects — a modern SaaS project management platform built with Laravel, inspired by tools like Trello, Asana, and Monday.com.

## What it does

FlowSphere gives teams, freelancers, and clients a single connected workspace for projects, tasks, deadlines, and team collaboration — instead of scattering that work across spreadsheets, chat apps, and missed deadlines.

## Features

- **Workspaces** — Company, Startup, Personal, or Freelance, with role-based membership (owner/manager/member/client)
- **Projects & Kanban tasks** — project cards with live progress and task boards (To Do / Doing / Done); task checklists and comments are available through the JSON API
- **Dashboard** — active projects, completed tasks, team size, upcoming deadlines, recent activity, team productivity
- **Flow AI Assistant** — ask a project "why is this delayed?" and get a plain-language diagnosis with recommendations. The diagnosis is currently rule-based (overdue tasks, past deadlines, overloaded members, inactivity); `AIService::askFlowAI()` is the single place to swap in a real LLM call
- **Project Health Score** — an automatic 0–100 score derived from task completion, deadlines, team workload, and activity
- **Smart Workload Management** — per-member utilization and rebalancing recommendations
- **Session-based authentication** — Laravel Sanctum SPA-style auth (register, login, logout)

## Tech stack

- **Backend**: Laravel 13, PHP 8.3, Laravel Sanctum
- **Frontend**: Blade components, Tailwind CSS v4, Vite
- **Database**: SQLite by default (swappable via `.env`)
- **Testing & CI**: PHPUnit, GitHub Actions (Ubuntu)

## Getting started

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate

composer run dev
```

On Windows PowerShell, create the database file with `New-Item database/database.sqlite` instead of `touch`, and use `copy` instead of `cp`.

`composer run dev` runs the Laravel server, queue listener, log watcher, and Vite dev server together. The app is then available at `http://127.0.0.1:8000`.

## Testing

```bash
php artisan test
```

The feature tests cover registration, login and logout (both form and JSON requests), workspace creation, listing and switching, and the owner/manager/member/client permissions in `WorkspacePolicy`.

Tests run against an in-memory SQLite database (see `phpunit.xml`). The same suite runs on Linux (`ubuntu-latest`) via GitHub Actions on every push to `main` and every pull request — see `.github/workflows/tests.yml`.

## Project structure

```
app/
  Http/Controllers/   Thin controllers (Dashboard, Project, Task, Team, Workspace, Auth)
  Http/Requests/       Form Request validation
  Models/              User, Workspace, Project, Task, TaskComment, Attachment, Activity, Notification, ...
  Policies/            Authorization per workspace role
  Services/            AIService, ReportService, NotificationService
.github/workflows/     CI: runs the PHPUnit suite on Ubuntu
resources/
  views/components/    Reusable Blade components (button, input, card, badge, layouts)
  views/                Auth, onboarding, dashboard, and project pages
routes/
  web.php               Blade-rendered pages (session auth)
  api.php               JSON API (Sanctum-protected)
tests/Feature/          Auth, workspace, and policy tests
```

## Status

Actively under development. Built as a portfolio-grade demonstration of Laravel fundamentals — authentication, authorization policies, Eloquent relationships, service-layer architecture, and a Blade + Tailwind frontend — rather than a toy CRUD app.

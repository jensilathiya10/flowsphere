# FlowSphere

One workspace to plan, build, and deliver projects — a modern SaaS project management platform built with Laravel, inspired by tools like Trello, Asana, and Monday.com.

## What it does

FlowSphere gives teams, freelancers, and clients a single connected workspace for projects, tasks, deadlines, and team collaboration — instead of scattering that work across spreadsheets, chat apps, and missed deadlines.

## Features

- **Workspaces** — Company, Startup, Personal, or Freelance, with role-based membership (owner/manager/member/client)
- **Projects & Kanban tasks** — project cards with live progress, task boards (To Do / Doing / Done), checklists, comments
- **Dashboard** — active projects, completed tasks, team size, upcoming deadlines, recent activity, team productivity
- **Flow AI Assistant** — ask a project "why is this delayed?" and get a plain-language diagnosis with recommendations
- **Project Health Score** — an automatic 0–100 score derived from task completion, deadlines, team workload, and activity
- **Smart Workload Management** — per-member utilization and rebalancing recommendations
- **Session-based authentication** — Laravel Sanctum SPA-style auth (register, login, logout)

## Tech stack

- **Backend**: Laravel 13, PHP 8.3, Laravel Sanctum
- **Frontend**: Blade components, Tailwind CSS v4, Vite
- **Database**: SQLite by default (swappable via `.env`)

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

`composer run dev` runs the Laravel server, queue listener, log watcher, and Vite dev server together. The app is then available at `http://127.0.0.1:8000`.

## Project structure

```
app/
  Http/Controllers/   Thin controllers (Dashboard, Project, Task, Team, Workspace, Auth)
  Http/Requests/       Form Request validation
  Models/              User, Workspace, Project, Task, Comment, Attachment, Activity, Notification, ...
  Policies/            Authorization per workspace role
  Services/            AIService, ReportService, NotificationService
resources/
  views/components/    Reusable Blade components (button, input, card, badge, layouts)
  views/                Auth, onboarding, dashboard, and project pages
routes/
  web.php               Blade-rendered pages (session auth)
  api.php               JSON API (Sanctum-protected)
```

## Status

Actively under development. Built as a portfolio-grade demonstration of Laravel fundamentals — authentication, authorization policies, Eloquent relationships, service-layer architecture, and a Blade + Tailwind frontend — rather than a toy CRUD app.

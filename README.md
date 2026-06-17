<div align="center">

# SentosaQuiz

**A multi-role quiz management platform built for SMA Sentosa Jakarta Barat**

Replaces manual, paper-based quiz administration with a centralized system for quiz creation, class organization, and chapter-level performance analysis.

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v3-F59E0B?style=flat)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-v3-4E56A6?style=flat&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Neon-336791?style=flat&logo=postgresql&logoColor=white)](https://neon.tech)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

[**Live Demo**](#) · [Report a Bug](#) · [Request a Feature](#)

</div>

---

## Live Demo

The application is deployed and fully functional. Use the credentials below to explore each role.

| Role    | Email                      | Password   |
| ------- | -------------------------- | ---------- |
| Admin   | `admin@sentosaquiz.demo`   | `demo1234` |
| Teacher | `teacher@sentosaquiz.demo` | `demo1234` |
| Student | `student@sentosaquiz.demo` | `demo1234` |

> Demo data resets periodically. If something looks off, it will be back to a clean state shortly.

**Suggested demo flow:**

1. Log in as **Teacher** → create a quiz → tag questions to a chapter → generate a class join code
2. Log in as **Student** → join the class with the code → take the quiz
3. Check the **Student** dashboard for per-chapter performance breakdown
4. Log in as **Admin** → view school-wide and per-class analytics

---

## Preview

> _Screenshot/GIF: Teacher creates a quiz → Student joins via class code → Student views chapter-based results._
>
> `![SentosaQuiz demo](./docs/demo.gif)`

---

## Table of Contents

- [Problem & Context](#problem--context)
- [Architecture Decisions](#architecture-decisions)
- [Features by Role](#features-by-role)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Known Constraints (MVP Scope)](#known-constraints-mvp-scope)
- [Roadmap](#roadmap)
- [Local Setup](#local-setup)
- [License](#license)

---

## Problem & Context

SMA Sentosa previously ran quizzes manually — paper-based or scattered across spreadsheets, with no consistent way to track which topics students were struggling with at a granular level. SentosaQuiz centralizes this into a single platform with three distinct roles (Admin, Teacher, Student), structured quiz creation, class-based organization, and a chapter-level analysis layer that identifies each student's strongest and weakest topics per attempt — the foundation for a planned learning-evaluation feature based on wrong-answer patterns.

## Architecture Decisions

These are the non-obvious decisions worth highlighting — what was chosen, and what it cost.

**1. PostgreSQL (Neon) instead of MySQL**
Laravel's tooling and community examples are overwhelmingly MySQL-first, so this was a deliberate trade-off in exchange for Neon's serverless/branching workflow. It surfaced real compatibility issues that wouldn't exist on MySQL: Filament's `orderByPivot()` helper is MySQL-specific and had to be replaced with a manual `orderBy('quiz_questions.order')`; local development on Windows/Laragon required manually enabling the `pdo_pgsql` / `pgsql` PHP extensions; and Neon requires `sslmode=require` in the database config. None of this is exposed to the end user, but it's the kind of friction that doesn't show up in tutorials.

**2. School-centric question storage instead of a global question bank**
Questions belong to a single quiz rather than living in a shared, reusable bank. This simplifies ownership and avoids a more complex permissions model, at the cost of duplicating questions if a teacher wants to reuse one across quizzes. The trade-off is mitigated by a `QuestionCopyService` that handles copy-on-edit: editing a question that's referenced elsewhere creates a new copy instead of mutating shared state.

**3. Two-step chapter tagging instead of tagging at question-creation time**
Questions are created untagged, then assigned to a chapter afterward through a dedicated Livewire modal (pick a chapter → assign untagged questions to it). This decouples question writing from curriculum organization and keeps the modal's candidate list clean — already-tagged questions disappear from subsequent opens. This tagging step is what makes the chapter-level analytics layer possible; getting the data model right here was treated as foundational, not an afterthought.

**4. Unified login with guard-based redirect instead of separate login pages per role**
All roles authenticate through a single `/login` endpoint. Admins and teachers land on the Filament dashboard (gated via `authGuard('web')` alignment), while students are redirected to a separate `/student/dashboard`. This avoids the complexity of multiple auth flows while still giving each role a tailored experience.

**5. A `type` field enforcing one quiz type per subject**
A unique constraint on `(subject_id, type)` enforces that a subject can only have one quiz of a given type. It looks like a minor schema detail, but removing it silently breaks the two-quiz-per-subject rule — a reminder that some "small" fields are load-bearing.

## Features by Role

### Admin

- School-wide performance dashboard
- Per-class and per-subject score averages
- Bottom-5 weakest chapters across the school
- Full visibility into subjects, chapters, and user management

### Teacher

- Create and manage quizzes (multiple-choice questions)
- Drag-and-drop question reordering (SortableJS + Livewire sync)
- Import/export questions via Excel (`maatwebsite/excel`)
- Tag questions to chapters via a two-step assignment modal
- Generate a 6-digit alphanumeric class join code for students
- View class-level and individual student performance

### Student

- Self-registration (email-only for MVP)
- Join a class using a teacher-issued join code (one class per student)
- Take assigned quizzes
- View a personal profile with per-quiz stats
- Chapter-level breakdown per attempt: correct-percentage by chapter, naturally sorted, with "Uncategorized" pinned last, highlighting best and weakest chapters

## Tech Stack

| Layer           | Technology                       |
| --------------- | -------------------------------- |
| Framework       | Laravel                          |
| Admin Panel     | Filament v3                      |
| Interactivity   | Livewire v3 + Alpine.js          |
| Database        | PostgreSQL (hosted on Neon.tech) |
| Authorization   | Spatie Laravel Permission        |
| Spreadsheet I/O | maatwebsite/excel                |
| Drag & Drop     | SortableJS (Filament-bundled)    |
| Dev Environment | Windows + Laragon (PHP 8.x)      |

No Vue.js or Axios — interactivity is handled entirely through Livewire and Alpine.

---

## Project Structure

```
app/
├── Filament/
│   └── Resources/
│       └── SubjectResource/
│           └── RelationManagers/   # Chapter CRUD lives here
├── Livewire/                       # Quiz builder, chapter-tagging modal, etc.
├── Services/
│   ├── QuestionCopyService.php     # Copy-on-edit for shared questions
│   └── ChapterAnalysisService.php  # Per-chapter scoring & breakdown logic
├── Models/
└── Http/Controllers/
```

---

## Known Constraints (MVP Scope)

These are deliberate trade-offs for MVP velocity, not oversights — documented here so they're not mistaken for bugs:

- **Relaxed validation on several fields.** Some fields (e.g. `questions.chapter_id`) are nullable with `nullOnDelete()` rather than strictly required, prioritizing working functionality over strict data integrity at this stage.
- **Single class per student, enforced at the application layer.** This matches the school's actual structure (one class per student) but isn't yet enforced with a database-level constraint.
- **Email-only student registration.** No school-ID verification or invite-gating yet — acceptable for MVP testing, but would need hardening before being opened to a full student body.

---

## Roadmap

- [ ] Finalize and ship the quiz creation overhaul (in-progress refinements to question card UI)
- [ ] Expand the question bank / chapter-tagging workflow
- [ ] **Learning evaluation feature** — surfacing recurring wrong-answer patterns per chapter over time, not just per attempt
- [ ] Tighten MVP-era validation (see Known Constraints) as usage scales

---

## Local Setup

### Requirements

- PHP 8.x
- Composer
- PostgreSQL (or a Neon.tech account)
- Node.js (for frontend asset compilation)

### Installation

```bash
git clone https://github.com/<your-username>/sentosaquiz.git
cd sentosaquiz

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

### Database Configuration

Set your PostgreSQL credentials in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=<your-neon-host>
DB_PORT=5432
DB_DATABASE=<your-database>
DB_USERNAME=<your-username>
DB_PASSWORD=<your-password>
```

If hosting on Neon.tech, SSL is required — add this to `config/database.php` under the `pgsql` connection:

```php
'sslmode' => 'require',
```

Then run migrations and seed demo data:

```bash
php artisan migrate --seed
```

### Windows / Laragon Note

PostgreSQL support isn't enabled by default on Laragon's bundled PHP. Enable the following in `php.ini`, then restart Laragon:

```ini
extension=pdo_pgsql
extension=pgsql
```

### Run

```bash
php artisan serve
npm run dev
```

Visit `http://localhost:8000` and log in with any of the [demo credentials](#live-demo) above, or your own seeded data.

### Useful Commands

```bash
php artisan optimize:clear   # Clear all caches (config, route, view, etc.)
```

---

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

<div align="center">

# SentosaQuiz

**A multi-role quiz management platform built for SMA Sentosa Jakarta Barat**

Replaces manual, paper-based quiz administration with a centralized system for quiz creation, class organization, and chapter-level performance analysis.

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v3-F59E0B?style=flat)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-v3-4E56A6?style=flat&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Neon-336791?style=flat&logo=postgresql&logoColor=white)](https://neon.tech)
[![Redis](https://img.shields.io/badge/Redis-Upstash-DC382D?style=flat&logo=redis&logoColor=white)](https://upstash.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

[**Live Demo**](#https://sentosa-quiz.free.laravel.cloud/)

<!-- · [Report a Bug](#) · [Request a Feature](#) -->

</div>

---

## Live Demo

The application is deployed and fully functional. There are **two ways** to try it:

### One-click Demo (No Registration)

Click a button on the landing page and you're instantly logged in as a fresh, isolated demo account.  
No form, no password, no cleanup needed on your end.

| Button             | What you get                                                                                                                    |
| ------------------ | ------------------------------------------------------------------------------------------------------------------------------- |
| **Try as Teacher** | Hits `/demo-login/teacher` → creates a scoped demo teacher account → lands on the Filament admin panel                          |
| **Try as Student** | Hits `/demo-login/student` → creates a scoped demo student account → auto-joins the Demo Class → lands on the student dashboard |

> Demo accounts and their data are **auto-purged after 24 hours** (or immediately on logout) via a scheduled cleanup job. No persistent data accumulation.

## Suggested Demo Flow

### 🧑‍🏫 As Teacher (one-click or fixed credential)

1. Click **Try as Teacher** on the landing page
2. You land in the **Filament admin panel** → navigate to **Quizzes**
3. Create a new quiz — give it a title and assign it to a subject
4. In the question builder:
    - **Add questions manually**, or
    - **Import from Excel** using the prepared template — download it from the link below, fill it in, and upload
5. Reorder questions with **drag-and-drop**
6. Open the **Chapter Tagging modal** to tag questions to chapters (enables per-chapter analytics)
7. Assign the quiz to the **Demo Class** using the class dropdown

**📥 Excel Question Import Template**

Download the mock template (pre-filled with sample questions) and upload it directly in the quiz builder:

> 🔗 **[Download Excel Template (Google Drive mock)](https://docs.google.com/spreadsheets/d/1RPY7uI0dpkjXe5Fei5LLbmymt6-A42kc/edit?usp=sharing&ouid=108824727547272959236&rtpof=true&sd=true)**

Or generate a fresh blank template from within the app:  
**Quizzes → [your quiz] → Manage Questions → Download Template**

The template columns are:

| Column           | Description                              | Example                             |
| ---------------- | ---------------------------------------- | ----------------------------------- |
| `No`             | Row number (optional, ignored on import) | `1`                                 |
| `Question`       | The question body                        | `What is the capital of Indonesia?` |
| `Option A`       | Choice A                                 | `Jakarta`                           |
| `Option B`       | Choice B                                 | `Bandung`                           |
| `Option C`       | Choice C                                 | `Surabaya`                          |
| `Option D`       | Choice D                                 | `Medan`                             |
| `Correct Answer` | Must be exactly `A`, `B`, `C`, or `D`    | `A`                                 |

> Rows with a blank `Question` or invalid `Correct Answer` are silently skipped on import.

---

### 🧑‍🎓 As Student (one-click or fixed credential)

1. Click **Try as Student** on the landing page
2. You land on the **student dashboard** — already enrolled in the Demo Class
3. Open the assigned **Demo Quiz** and take it
4. After submission, check your **Profile / Results** page:
    - Per-quiz score
    - **Chapter-level breakdown**: correct-percentage per chapter, naturally sorted, with "Uncategorized" pinned last, and best/weakest chapters highlighted

---

### 🛡️ As Admin (fixed credential only)

1. Log in as `admin@sentosaquiz.demo` / `demo1234`
2. Explore the **school-wide analytics dashboard**:
    - Per-class and per-subject score averages
    - Bottom-5 weakest chapters across the school
3. Manage subjects, chapters, and users from the sidebar

> Admin analytics exclude all demo activity (`is_demo = true` records are filtered out), so the numbers reflect only the fixed seeded school data.

---

## Preview

> _Screenshot/GIF: Teacher creates a quiz → imports from Excel → Student joins via demo flow → Student views chapter-based results._
>
> `![SentosaQuiz demo](./docs/demo.gif)`

---

## Table of Contents

- [Problem & Context](#problem--context)
- [Architecture](#architecture)
- [Features by Role](#features-by-role)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Known Constraints (MVP Scope)](#known-constraints-mvp-scope)
- [Local Setup](#local-setup)
- [License](#license)

---

## Problem & Context

SMA Sentosa previously ran quizzes manually — paper-based or scattered across spreadsheets, with no consistent way to track which topics students were struggling with at a granular level. SentosaQuiz centralizes this into a single platform with three distinct roles (Admin, Teacher, Student), structured quiz creation, class-based organization, and a chapter-level analysis layer that identifies each student's strongest and weakest topics per attempt — the foundation for a planned learning-evaluation feature based on wrong-answer patterns.

---

## Architecture

| Layer                       | Technology                    | Notes                                     |
| --------------------------- | ----------------------------- | ----------------------------------------- |
| Backend                     | Laravel (PHP 8.3+)            | MVC, Eloquent ORM                         |
| Admin Panel                 | Filament v3                   | Teacher & admin interfaces                |
| Interactivity               | Livewire v3 + Alpine.js       | Quiz builder, chapter tagging             |
| Auth backend                | Laravel Fortify               | Headless auth (login, register, reset)    |
| Authorization               | Spatie Laravel Permission     | RBAC — Admin / Teacher / Student roles    |
| Database                    | PostgreSQL on Neon            | Serverless, SSL required                  |
| Caching / Sessions / Queues | Redis on Upstash              | Reduces Neon round-trips; session storage |
| Spreadsheet I/O             | maatwebsite/excel             | Import questions + export template        |
| Drag & Drop                 | SortableJS (Filament-bundled) | Question reordering                       |
| Styling                     | Tailwind CSS v4               | Utility-first                             |
| Build Tool                  | Vite                          | HMR in dev, bundled for prod              |
| Dev Environment             | Windows + Laragon (PHP 8.x)   |                                           |

---

## Features by Role

### Admin

- School-wide performance dashboard
- Per-class and per-subject score averages
- Bottom-5 weakest chapters across the school
- Full visibility into subjects, chapters, and user management
- All analytics exclude `is_demo` records — real data only

### Teacher

- Create and manage quizzes (multiple-choice questions)
- Drag-and-drop question reordering (SortableJS + Livewire sync)
- Import questions via Excel template (`maatwebsite/excel`)
- Export a blank template directly from the quiz builder
- Tag questions to chapters via a two-step assignment modal
- Assign quizzes to classes
- View class-level and individual student performance
- Demo teachers are scoped to their own session — they never see other demo teachers' quizzes

### Student

- Self-registration (email-only for MVP)
- One-click demo login (no registration required)
- Auto-join a class via the demo flow, or join with a teacher-issued code
- Take assigned quizzes
- View a personal profile with per-quiz stats
- **Chapter-level breakdown per attempt**: correct-percentage by chapter, naturally sorted, with "Uncategorized" pinned last, highlighting best and weakest chapters

---

## Project Structure

```
app/
├── Filament/
│   └── Resources/
│       └── SubjectResource/
│           └── RelationManagers/   # Chapter CRUD lives here
├── Livewire/                       # Quiz builder, chapter-tagging modal, etc.
├── Imports/
│   └── QuestionImport.php          # Excel → Question + McqOption rows
├── Exports/
│   └── QuestionTemplateExport.php  # Blank template with sample rows & bold header
├── Services/
│   ├── QuestionCopyService.php     # Copy-on-edit for shared questions
│   └── ChapterAnalysisService.php  # Per-chapter scoring & breakdown logic
├── Models/
├── Console/
│   └── Commands/
│       └── DemoCleanup.php         # Artisan: demo:cleanup (runs hourly)
└── Http/
    └── Controllers/
        └── DemoController.php      # /demo-login/teacher & /demo-login/student
```

---

## Known Constraints (MVP Scope)

These are deliberate trade-offs for MVP velocity, not oversights — documented here so they're not mistaken for bugs:

- **Relaxed validation on several fields.** Some fields (e.g. `questions.chapter_id`) are nullable with `nullOnDelete()` rather than strictly required, prioritizing working functionality over strict data integrity at this stage.
- **Single class per student, enforced at the application layer.** This matches the school's actual structure (one class per student) but isn't yet enforced with a database-level constraint.
- **Email-only student registration.** No school-ID verification or invite-gating yet — acceptable for MVP testing, but would need hardening before being opened to a full student body.
- **Demo teacher quizzes are not visible to demo students.** Demo students always see the fixed, pre-seeded demo quiz — teacher-created demo quizzes are preview-only for the teacher session that created them.

---

## Local Setup

### Requirements

- PHP 8.3+
- Composer
- PostgreSQL (or a [Neon.tech](https://neon.tech) account)
- Redis (or an [Upstash](https://upstash.com) account)
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

Set your PostgreSQL and Redis credentials in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=<your-neon-host>
DB_PORT=5432
DB_DATABASE=<your-database>
DB_USERNAME=<your-username>
DB_PASSWORD=<your-password>

REDIS_HOST=<your-upstash-host>
REDIS_PASSWORD=<your-upstash-password>
REDIS_PORT=6379

# Demo mode (set true to enable /demo-login/* routes)
DEMO_ENABLED=false
```

If hosting on Neon.tech, SSL is required — add this to `config/database.php` under the `pgsql` connection:

```php
'sslmode' => 'require',
```

Then run migrations and seed:

```bash
# Core schema + roles + admin user
php artisan migrate --seed

# Fixed demo class & demo quiz (run once after migrate; idempotent)
php artisan db:seed --class=DemoSeeder
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

Visit `http://localhost:8000` and log in with any of the [demo credentials](#option-b--fixed-credentials) above, or your own seeded data.

### Useful Commands

```bash
php artisan optimize:clear      # Clear all caches (config, route, view, etc.)
php artisan demo:cleanup        # Manually trigger demo data purge (normally runs hourly)
php artisan schedule:work       # Run the scheduler locally (triggers demo:cleanup hourly)
```

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

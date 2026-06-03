# SentosaQuiz Architecture Overview

This document provides a concise overview of the core technologies and architecture used in the SentosaQuiz application.

## 1. Frontend Architecture
- **Framework:** **Livewire (v4)** combined with **Flux** for building dynamic, server-rendered UIs without needing heavy custom JavaScript.
- **Admin Panel:** **Filament** for rapidly building robust and feature-rich administrative interfaces.
- **Styling:** **Tailwind CSS (v4)** for a utility-first styling approach, ensuring a modern and responsive design.
- **Build Tool:** **Vite** for fast and efficient frontend asset bundling and Hot Module Replacement (HMR).

## 2. Backend Architecture
- **Core Framework:** **Laravel (PHP 8.3+)**. The application follows the standard Laravel MVC (Model-View-Controller) architecture.
- **Data Handling:** **Maatwebsite Excel** (powered by PhpSpreadsheet) is utilized for robust import and export capabilities of Excel files.

## 3. Database & Storage
- **Primary Database:** **PostgreSQL**, hosted on **Neon Serverless Postgres**. 
- **In-Memory Store / Performance Optimization:** **Redis**, hosted on **Upstash**. Redis is a critical architectural component specifically implemented to optimize application performance and reduce database round-trips (especially to the remote Neon database). It is used for:
  - **Caching:** Caching expensive and heavy database queries (such as rendering the dashboard analytics) to reduce latency and load times.
  - **Sessions:** Fast, memory-based session storage, significantly improving the login process speed and overall user interaction compared to database sessions.
  - **Queues:** Managing asynchronous background jobs efficiently without straining the primary relational database.

## 4. Middleware & Security
- **Authentication:** **Laravel Fortify** serves as the headless authentication backend, providing secure registration, login, and password reset functionalities.
- **Authorization:** **Spatie Laravel Permission** handles Role-Based Access Control (RBAC), ensuring users can only access resources they are permitted to see.
- **Security Protections:**
  - Standard Laravel protections are active, including **CSRF token verification**, **XSS protection**, and **SQL injection prevention** (via Eloquent ORM).
  - **Password Hashing:** Passwords are encrypted using the **Bcrypt** algorithm.

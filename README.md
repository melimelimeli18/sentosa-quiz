# Sentosa Quiz

## Table of Contents
- [Description](#description)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)

## Description
Sentosa Quiz is a robust, dynamic web-based student assessment application. It is built to facilitate seamless quiz management, delivery, and reporting. The platform serves administrators and students, solving the core problem of managing complex quiz data and users efficiently while ensuring rapid performance. 

The application utilizes a modern tech stack featuring Laravel (PHP), Livewire, and Filament for the admin interface. To ensure optimal performance and fast page loads, it integrates Neon Serverless Postgres for the primary database and Upstash Redis for high-speed session management, queueing, and caching.

## Prerequisites
Before you begin, ensure you have the following installed on your system:
- **PHP** ^8.3
- **Composer** (PHP dependency manager)
- **Node.js** and **npm** (for frontend asset compilation)
- **PostgreSQL** (or a remote Neon DB instance)
- **Redis** (or a remote Upstash Redis instance)

## Installation

Follow these steps to set up the project locally:

```bash
# 1. Clone the repository
git clone <repository-url>
cd sentosa-quiz

# 2. Install PHP dependencies
composer install

# 3. Install NPM dependencies
npm install

# 4. Set up environment variables
cp .env.example .env

# 5. Generate the application key
php artisan key:generate

# 6. Run database migrations (make sure DB credentials are set in .env first)
php artisan migrate

# 7. Build the frontend assets
npm run build
```

*Note: Alternatively, you can run `composer setup` if your environment supports the configured Composer setup script.*

## Usage

To start the application locally and begin developing or testing:

```bash
# Start the Laravel development server and Vite frontend server concurrently
composer dev
```

Once running, you can access the application at `http://localhost:8000`.

**Configuration Options:**
Ensure you configure your `.env` file with the correct database and Redis credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=your-neon-host
DB_PORT=5432
DB_DATABASE=sentosa_quiz
DB_USERNAME=your-username
DB_PASSWORD=your-password

REDIS_CLIENT=phpredis
REDIS_HOST=your-upstash-host
REDIS_PASSWORD=your-upstash-password
REDIS_PORT=your-upstash-port
```

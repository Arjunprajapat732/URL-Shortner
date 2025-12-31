# URL Shortener Service

A Laravel-based URL shortener service with role-based access control, supporting multiple companies and users.

## Requirements

- PHP 8.2 or higher
- Composer
- SQLite (default) or MySQL
- Node.js and NPM

## Setup & Run Project

### Step 1: Install Dependencies

```bash
composer install
npm install
```

### Step 2: Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

### Step 3: Setup Database

```bash
php artisan migrate
php artisan db:seed
```

### Step 4: Build Frontend Assets

```bash
npm run build
```

### Step 5: Start Server

```bash
php artisan serve
```

**Application will be available at:** `http://localhost:8000`

## Login

After setup, login with:

| Role | Email | Password |
|------|-------|----------|
| SuperAdmin | `superadmin@example.com` | `password` |
| Admin | `admin@testcompany.com` | `password` |
| Member | `member@testcompany.com` | `password` |

## Features

- **Role-Based Access Control**: SuperAdmin, Admin, Member roles
- **Multi-Company Support**: Each company can have multiple users
- **Invitation System**: SuperAdmin invites Admin (new company), Admin invites Admin/Member (same company)
- **URL Shortening**: Admin and Member can create short URLs
- **Date Filtering**: Filter short URLs by Today, Last Week, Last Month
- **CSV Download**: Download filtered short URLs as CSV
- **Public Redirection**: Short URLs accessible at `/s/{shortCode}`

## Quick Commands

```bash
# Run tests
php artisan test

# Clear cache
php artisan cache:clear

# Reset database
php artisan migrate:fresh && php artisan db:seed
```

## License

MIT License

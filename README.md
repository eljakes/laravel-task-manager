# Laravel Task Manager

A simple Laravel task management app built for the assignment requirements.

## Features

- Create, edit, and delete projects.
- Create, edit, and delete tasks.
- Tasks store a name, project, priority, and Laravel timestamps.
- Tasks are listed by priority within their project.
- Drag and drop tasks in the browser to update priorities automatically.
- Filter the task list by project with a dropdown.
- Data is stored in MySQL using Laravel migrations and Eloquent models.

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- MySQL 8 or compatible MariaDB

This project currently uses Laravel 13, which satisfies the Laravel 11+ requirement.

## Local Setup

1. Install dependencies:

```bash
composer install
npm install
```

2. Create the environment file:

```bash
cp .env.example .env
php artisan key:generate
```

3. Create a MySQL database:

```sql
CREATE DATABASE laravel_task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Update `.env` if your MySQL credentials are different:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_task_manager
DB_USERNAME=root
DB_PASSWORD=
```

5. Run the database migrations:

```bash
php artisan migrate
```

6. Start the local development server:

```bash
php artisan serve
```

Open the localhost URL shown in the terminal, usually:

```text
http://127.0.0.1:8000
```

## Using the App

1. Create a project from the Projects page.
2. Go to the Tasks page.
3. Select a project from the dropdown.
4. Add tasks to that project.
5. Drag task rows up or down to reorder them. The top row becomes priority `1`, the next row priority `2`, and so on.

## Testing

Run the automated test suite:

```bash
php artisan test
```

The tests use an in-memory SQLite database so they do not modify your local MySQL data.

## Deployment Notes

For a simple production deployment:

1. Upload the project to your server.
2. Run `composer install --no-dev --optimize-autoloader`.
3. Run `npm install` and `npm run build`.
4. Configure the production `.env` file with MySQL credentials and `APP_ENV=production`.
5. Run `php artisan key:generate` if the app key has not been set.
6. Run `php artisan migrate --force`.
7. Point the web server document root to the `public` directory.
8. Run `php artisan config:cache` and `php artisan route:cache`.

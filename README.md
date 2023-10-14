# Laravel 8 Project

Welcome to the Laravel 8 project. This README provides instructions for setting up, configuring, and running the project.

## Prerequisites

Before you begin, ensure you have met the following requirements:

- PHP (>= 8.1)
- Composer (https://getcomposer.org/)
- Laravel (10.x)
- Database server (e.g., MySQL,)
- Git

## Getting Started

1. Clone the repository:

   ```bash
   git clone https://github.com/progRizvi/assessment.git

    cd assessment
    ```
2. Install dependencies:

   ```bash
   composer install
   ```
3. Create a copy of the `.env.example` file and rename it to `.env`:

   ```bash
    cp .env.example .env
    ```
4. Generate an app encryption key:

    ```bash
    php artisan key:generate
    ```
5. Create an empty database for the application.
6. In the `.env` file, add database information to allow Laravel to connect to the database.
7. Migrate the database:

    ```bash
    php artisan migrate
    ```
8. Run the application:

    ```bash
    php artisan serve
    ```
9. You can now access the server at http://localhost:8000


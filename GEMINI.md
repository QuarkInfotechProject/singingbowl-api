# Cartnext Project Analysis

## Project Overview

This project is a modular e-commerce platform built with the Laravel framework. It utilizes a variety of modern technologies and follows a modular architecture to ensure scalability and maintainability.

### Key Technologies

*   **Backend:** PHP 8.4, Laravel 12
*   **Database:** PostgreSQL
*   **Web Server:** Nginx
*   **Containerization:** Docker
*   **Search:** Meilisearch
*   **API Documentation:** L5 Swagger

### Architecture

The application is structured using the `nwidart/laravel-modules` package, which allows for a clean separation of concerns. Each module (e.g., `Product`, `Order`, `User`) encapsulates a specific domain of the application, with its own routes, controllers, models, and views. This modular approach makes it easy to develop, test, and maintain individual features without affecting the rest of the application.

## Building and Running

The project is designed to be run with Docker. The following commands are essential for getting the application up and running:

*   **Initial Startup:**
    ```bash
    docker-compose up --init
    ```
*   **Regular Startup:**
    ```bash
    docker-compose up
    ```
*   **Accessing the Container:**
    ```bash
    docker compose exec --user root system /bin/bash
    ```
*   **Installing Dependencies:**
    ```bash
    docker compose exec --user root system /bin/sh -c "composer install"
    ```

## Development Conventions

*   **Modular Development:** All new features should be developed within a new or existing module.
*   **API-First Approach:** The application exposes a comprehensive API, which is the primary way to interact with the backend. All API routes are defined within the `Routes/api.php` file of each module.
*   **Coding Style:** The project follows the PSR-12 coding style guide, which is enforced by `laravel/pint`.
*   **Database Migrations:** All database schema changes are managed through Laravel's migration system.
*   **Environment Variables:** The application uses a `.env.local` file for local development, which is based on the `.env.example` file.

## Key Files and Directories

*   `system/`: This directory contains the core Laravel application.
*   `system/Modules/`: This directory contains all the application's modules.
*   `system/composer.json`: This file lists all the project's dependencies.
*   `docker-compose.example`: This file defines the Docker services for the application.
*   `system/Dockerfile_dev`: This file defines the Docker image for the development environment.
*   `readme.md`: This file contains basic setup instructions.

# URL Shortener Documentation

## Table of Contents
- [Prerequisites](#prerequisites)
- [Initializing the Laravel App](#initializing-the-laravel-app)
  - [Setting Up Environment File](#setting-up-environment-file)
  - [Installation Steps](#installation-steps)
- [Running Tests](#running-tests)
  - [PHPUnit Tests](#phpunit-tests)
  - [Postman Integration Tests](#postman-integration-tests)
  - [Testing via CURL from terminal](#testing-via-curl-from-terminal)
- [Verifying Service Availability](#verifying-service-availability)
- [Installation Guide for Newbies](#installation-guide-for-newbies)
  - [Environment Setup](#environment-setup)
    - [Linux/Windows Setup](#linuxwindows-setup)
  - [Docker Setup (Ubuntu/Debian)](#docker-setup-ubuntudebian)


## Prerequisites
- PHP 8.2 or later (may function on PHP 8.0 but not tested)
- Git
- Composer
- PHP Zip extension

## Initializing the Laravel App

### Setting Up Environment File

For first-time use, you need to set up the environment file:
- Create a `.env` file if none exists
- Replace its contents with the contents of `.env.simple-example`

### Install Laravel Dependencies

#### For Docker Deployment:
1. Access the terminal of the Docker service:
   ```
   sudo docker exec -it url_short /bin/bash
   ```
2. Install dependencies:
   ```
   composer install
   ```
3. Generate application keys:
   ```
   php artisan key:generate
   ```
4. Verify routes:
   ```
   php artisan route:list
   ```

#### For Non-Docker Deployment:
1. Navigate to the Laravel root directory (laravel-app)
2. Install dependencies:
   ```
   composer install
   ```
3. Generate application keys:
   ```
   php artisan key:generate
   ```
4. Verify routes:
   ```
   php artisan route:list
   ```
5. Start a local server:
   ```
   php artisan serve
   ```

## Running Tests

### PHPUnit Tests
Run the PHPUnit tests with:
```
php artisan test
```

### Postman Integration Tests
Integration tests are available in the Postman collection, suitable for frontend developers:
[Postman Collection](https://www.postman.com/restless-sunset-44843/workspace/url-shortener/collection/31925882-d7636ed2-4143-43e3-bc20-65f846b47d47?action=share&creator=31925882)

### Testing via CURL from terminal
curl -X POST http://localhost/api/v1/encode      -H "Content-Type: application/json"      -d '{"url":"https://example.com/long/path"}'

curl -X POST http://localhost/api/v1/decode      -H "Content-Type: application/json"      -d '{"url":"http://short.est/4EIq1Q"}'

## Verifying Service Availability

Access the test endpoint to confirm service availability:

- For local development: http://127.0.0.1:8000/api/test
- For Docker deployment: http://127.0.0.1/api/test

## Installation Guide for Newbies

### Environment Setup

#### Linux/Windows Setup

1. Install PHP 8.2 or later
2. Add the PHP executable location to your system environment path variable
   - Example: Set environment path to `C:\php` if this is where your PHP executable is located
3. Install Git and Composer
4. Verify PHP and Composer installations:
   ```
   php -v
   composer -v
   ```
   If these commands fail, ensure your system path variable is properly set and restart your terminal or PC
5. Install PHP Zip extension:
   - Download the module if not present
   - Update your php.ini file to include:
     ```
     extension=zip
     ```
   - Test for the Zip extension:
     ```
     php -m | findstr zip   # Windows
     php -m | grep zip      # Linux
     ```
6. Clone the repository:
   ```
   git clone https://github.com/pat2echo/url-shortener.git
   ```

### Docker Setup (Ubuntu/Debian)

1. Ensure Docker and Docker Compose are installed:
   ```
   sudo docker version && docker-compose -v
   ```
   Verify that client & server exist in the output. If not installed:
   ```
   sudo apt-get update
   sudo snap install docker
   ```
2. Clone the repository:
   ```
   git clone https://github.com/pat2echo/url-shortener.git
   ```
3. Start the Docker service:
   - Navigate to the root directory containing the docker-compose.yml file
   - Run:
     ```
     sudo docker-compose up -d
     ```
   This starts the Docker service in the background and installs Composer

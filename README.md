

## About Repository

A very simple Laravel 8 + Vue 2 + AdminLTE 3 based Curd Starter template for SPA Application.
<p align="center">
<img src="https://i.imgur.com/mZAHbUL.png">
<img src="https://i.imgur.com/3hhoQnq.png">
<img src="https://i.imgur.com/aHtQkYl.png">
<img src="https://i.imgur.com/V7OuwLn.png">
</p>

## Tech Specification

- Laravel 8
- Vue 2 + VueRouter + vue-progressbar + sweetalert2 + laravel-vue-pagination
- Laravel Passport
- Admin LTE 3 + Bootstrap 4 + Font Awesome 5
- PHPUnit Test Case/Test Coverage

## Features

- Modal based Create+Edit, List with Pagination, Sortable
- Register by Social Account
- Notifications when login with slack, email, phone
- Multiple Languages with vueJS and Laravel
- Login, Register, Forget+Reset Password as default auth
- Profile, Update Profile, Change Password, Avatar
- Product Management 
- Search Product
- User Management
- Slide Management
- Menu Management
- Customer Management
- CRUD UserRole
- CRUD Cart
- Chart: Revenue, Customer
- Comment
- Import, Export CSV
- Laravel Service, Resource
- API Documentation with Swagger
- Frontend and Backend User ACL with Gate Policy (type: admin/user)
- Simple Static Dashboard
- Developer Options for OAuth Clients and Personal Access Token
- Build with Docker

## Installation

- `git clone https://github.com/huynhthaihuy64/Laravel-Vue.git`
- `cd laravel-vue/`
- `composer install`
- `cp .env.example .env`
- Update `.env` and set your database credentials
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan db:seed`
- `php artisan passport:install`
- `npm install`
- `npm run watch`
- `php artisan serve`

## Install with Docker

- `docker-compose up --build`
- `docker-compose up -d`
- `docker exec -it shop /bin/bash`
- `composer install`
- `cp .env.example .env`
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan db:seed`
- `php artisan passport:install`
- Application http://localhost:8083/
- DBhost: yourIP:3309, user: root, Password: 123456


## Unit Test

#### run PHPUnit

```bash
# run PHPUnit all test cases
vendor/bin/phpunit
# or Feature test only
vendor/bin/phpunit --testsuite Feature
```

#### Code Coverage Report

```bash
# reports is a directory name
vendor/bin/phpunit --coverage-html reports/
```
A `reports` directory has been created for code coverage report. Open the dashboard.html.


## License

[MIT license](https://opensource.org/licenses/MIT).

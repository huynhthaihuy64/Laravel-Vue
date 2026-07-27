

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
- Payment by Paypal, Momo, VNPay, OnePay
- Exchange Currency
- Chat with friend
- Upload file AWS S3
- Xhprof Test Performance
- Send SMS Vonage
- Integration Botman to shop
- Chat with Pusher

## Document Payment
- Paypal: srmklive/paypal, https://developer.paypal.com/: Login by account Personal,
- Google: https://console.cloud.google.com/
- Momo: 
    + test: https://developers.momo.vn/v3/vi/docs/payment/onboarding/test-instructions/
    + document: https://github.com/momo-wallet/php
    + Phone Success: "0919100100"
- VNPay: https://sandbox.vnpayment.vn/apis/docs/huong-dan-tich-hop/
- OnePay: https://mtf.onepay.vn/developer/?page=modul_noidia_php
- Exchange Currency: https://app.currencyapi.com/dashboard
- Pusher: https://www.youtube.com/watch?v=fFizsY6n8yY&list=PLRKk5RpSyRSVGzvQ8zzkLtb-cU_M6F9GK&index=36&ab_channel=webmobtuts. Config broadcast driver trong env thành pusher

## Library

- aws-sdk `sdk for aws`
- l5-swagger `document api`
- slack-notification `notification with slack`
- socialite `package for login social`
- vonage-notification-chanel `send sms`
- flysystem-aws-s3-v3 `upload s3 aws`
- maatwebsite `Excel`
- srmklive/paypal `payment with paypal`
- botman/botman `bot chat with laravel widget`
- botman/driver-web

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
- `php artisan sync:currency`

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
- `php artisan storage:link`

# Run UI VueJS:
- `npm run development` hoặc `npm run watch`

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


### install git hook: pre commit
- before all, we need ``composer install``
- after that copy ``git-hooks/pre-commit`` file to ``.git/hooks/pre-commit`` by command: ``cp git-hooks/pre-commit .git/hooks/pre-commit``
- set permission: ``chmod +x .git/hooks/pre-commit``
- if commit have errors, message like this:
  0 file committed, 1 file failed to commit: tesst Checking PHP Lint... No syntax errors detected in D:\CODE\kpi-service/app/Http/Controllers/Controller.php Running Code Sniffer. Code standard PSR12. [32mE[0m 1 / 1 (100%) [1mFILE: D:\CODE\kpi-service\app\Http\Controllers\Controller.php[0m ---------------------------------------------------------------------- [1mFOUND 2 ERRORS AFFECTING 2 LINES[0m ---------------------------------------------------------------------- 1 | [31mERROR[0m | [x] End of line character is invalid; expected "\n" but | | found "\r\n" 12 | [31mERROR[0m | [x] Each imported trait must have its own "use" import | | statement ---------------------------------------------------------------------- [1mPHPCBF CAN FIX THE 2 MARKED SNIFF VIOLATIONS AUTOMATICALLY[0m ---------------------------------------------------------------------- Time: 509ms; Memory: 8MB Fix the error before commit! Run ./vendor/bin/phpcbf --standard=PSR12 D:\CODE\kpi-service/app/Http/Controllers/Controller.php for automatic fix or fix it manually.
- please run command like: ``./vendor/bin/phpcbf --standard=PSR12 D:\CODE\kpi-service/app/Http/Controllers/Controller.php`` to fix it.

# SIMVEB Webservices

## Getting started

Requirements
* docker installed
* docker-composer installed
## Set up the app


 * `cp .env.example .env`
 * `docker-compose up -d`
 * `docker-compose exec app composer install`
 * `docker-compose exec app php artisan key:generate`
 * `docker-compose exec app php artisan storage:link`
 * `docker-compose exec app php artisan migrate`
 * `docker-compose exec app php artisan db:seed`
 * `docker-compose exec app php artisan passport:install`


## Running app

* Run `docker-compose up`
* Run `docker-compose up -d` to run the app in the background

App is served on port 8004

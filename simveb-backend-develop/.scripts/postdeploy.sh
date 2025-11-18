#!/bin/bash

# Migrate DB
php artisan migrate --force
php artisan db:seed

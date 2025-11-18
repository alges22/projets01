#!/bin/bash

chmod -R 777 public
chmod -R 777 storage

mkdir -p storage/app/public
mkdir -p storage/framework/{cache,sessions,testing,views}
mkdir -p storage/logs
cp -Rf wdsl/ storage/wdsl
php artisan storage:link
php artisan optimize

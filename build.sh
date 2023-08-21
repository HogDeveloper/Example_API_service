#!/bin/bash

mkdir database && 
mkdir database/mysql 
mkdir database/mysql/mysql-data
cp .env.example .env
docker compose up --build -d
docker exec -ti m2e_fpm composer install
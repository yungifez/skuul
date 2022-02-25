# Schooldash school management system

>In search of good school management systems written in laravel, I tried so many although most were quite remarkably good they lacked some essential features that I would have loved in a school management system.
This made me passionate in building my own school management system. Although it has been difficult, it's actually forming up into a quite useable project.

Schooldash is a multi school management system that aims to make administration and school 

## Requirements
* Php 8
* Composer 
* Since this project is running laravel 8, we suggest checking out 

## Installation
* Clone the repository by running the following command in your comamand line below (Or you can dowload zip file from github)

```shell
git clone https://github.com/yungifez/schooldash ./schooldash
 ```

* Head to the projects directory
```shell
cd schooldash
 ```

* Install composer dependancies
```shell
composer install
```
* Migrate the database
```shell
php artisan migrate
```

* Seed database
```shell
php artisan db:seed
 ```
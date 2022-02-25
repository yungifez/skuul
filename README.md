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
* Copy .env.example file into .env file and configure based on your environment
```shell
cp .emv.example .env
```
* Migrate the database
```shell
php artisan migrate
```
* Seed database
```shell
php artisan db:seed
```

After running the above commands, you should be able to access the application at http::/localhost or your designated domain name depending on configuration.

## Setup
* Log in to the application with the following credentials
    * Email: Super@admin.com
    * Password: password
    
    You would be authenticated and redirected to the dashboard
* Create a new school or work with the default school
* Proceed to view schools and from the drop down, set the school you want to work with. Now any action you carry out will be carried out on the set school
* Create an academic year and set it as the current academic year

## Usage
* Add class groups to the school
* Add classes to class groups
* Add sections to classes
* Add students to sections (You must have created a class and a section before you can add students)




# Skuul school management system

>In search of good school management systems written in laravel, I tried so many although most were quite remarkably good they lacked some essential features that I would have loved in a school management system.This made me passionate in building my own school management system. Although it has been difficult, it's actually forming up into a quite useable project.

![schooldash-dahboard-page](https://user-images.githubusercontent.com/63137056/216740379-18cb9f1d-5e80-4bc8-8b99-07d08ea98da4.png)


# CONGRATS V2 OF SKUUL IS OUT

Skuul is awesome, but it had a few shortcomings when it came to some areas like UI speed and application slugishness as it grows. Version 2 fixes these issues and also improves on accessibility. 

V2 is way faster and doesn't slow down condiderably as the app grows. Also upgrading should be relatively easy. Requirements remain the same with one exception, we now require node for asset bundling. No worries if you don't have node, there is a solution to that

### Skuul is a multi school management system that aims to make school administration and activities a breeze by using the power of the internet and increased connectinity

## Requirements
* Php 8.1 and above
* Composer 
* Since this project is running laravel 9, we suggest checking out the official requirements [here](https://laravel.com/docs/9.x/upgrade#updating-dependencies)
* Npm

## Installation
To skip steps 4 down ( after composer install ), you can run the below command and it would guide you through the process automatically
```shell
php artisan skuul:init
 ```
* Clone the repository by running the following command in your comamand line below (Or you can dowload zip file from github)
```shell
git clone https://github.com/yungifez/skuul ./skuul
 ```
* Head to the project's directory
```shell
cd skuul
 ```
* Install composer dependancies
```shell
composer install
```
* Copy .env.example file into .env file and configure based on your environment
```shell
cp .env.example .env
```
* Install node dependencies
```shell
cp  npm install
```
* Build NPM assets
```shell
  npm run build
```

Note if you do not have node, you can do this in your local environment and using an ftp program upload the publi/build folder and manifest.json folder to your server
* Generate encryption key
```shell
php artisan key:generate
```
* Migrate the database
```shell
php artisan migrate
```
* Seed database 
    
    You can seed the database in 2 ways
    - For production ie in your live server
        ```shell
        php artisan db:seed --class RunInProductionSeeder
        ```
    - For testing or development purposes
        ```shell
        php artisan db:seed
        ```
* Seed database to populate countries (takes approximately 10 minutes)
```shell
php artisan db:seed --class=WorldSeeder
```
* Set application logo by adding it in the public img folder and edit the .env logo path appropriately
* Store favicon in path public/favicons/, the file should be called favicon.ico
* For development or testing purposes, you can use the laravel built in server by running 
```shell
php artisan serve
```
If you are running on production, visit your domain to verify it is working 

After running the above commands, you should be able to access the application at http::/localhost or your designated domain name depending on configuration.

## Updating
Typically, you can update most of the time following these steps
- clone the new version
- composer update
- php artisan optimize:clear
- php artisan migrate (Make sure to backup database)
- php artisan db:seed --class RunInProductionSeeder
## Setup
* Log in to the application with the following credentials
    * Email: super@admin.com
    * Password: password

* You would be prompted to change your password, change your passsword in the profile page to continue

    
* if you are on production 
    - When you log in, you would be redirected to a page with error message at the top right corner that says "Please set your school of operation first". At the menu, click on create schools
    - On the page to create schools, provide a name, address and initial for your school and click on create school
    - Click on view schools, select the current school and click on the button set scvhool
    - You can now head over to the dashboard
    - You can begin to add classes, students, teachers etc. Some operations would not work specifically all links under the academics section
    - Head over to academic years, create a new academic year and a new semester then set the academic year and semester 
* if you are on dev or testing, data is preset to test and use the application.

## Usage
* Add class groups to the school
* Add classes to class groups
* Add sections to classes
* Add students to sections (You must have created a class and a section before you can add students)
* Add teachers to school
* Add subjects to school

## Features
### Super Admin
By default super admin can manage all activities in each school, some super admin exclusive features are
* Ability to create, edit and delete schools
" Ability to set school of operation

### Admin
* Ability to manage own school settings
* Ability to create, edit, view and delete class groups in assigned school
* Ability to create, edit, view and delete classes 
* Ability to create, edit, view and delete sections
* Ability to create, edit, view and delete classes
* Ability to create, edit, view and delete subjects
* Ability to create, edit, view and delete academic years
* Ability to set Academic years
* Ability to admit students, view student profiles, edit student profile, print student profile and delete student
* Ability to create teachers, edit teacher profile and delete teacher
* Ability to create, edit, manage, view and delete timetables
* Ability to create, edit, view and delete sylabii
* Ability to create, edit, view and delete semester
* Ability to set own school academic year and semester

### Teachers
* Ability to create, edit, view and delete sylabii
* Ability to create, edit, manage, view and delete timetables

This project was highly inspired by 4jean/lavSMS

Do you like the current state of this project?, you can support me or hire me for work

Todo
- Create demo site (for now, go to yungifez.xyz might be a mess but worth it. Log in to super admin account with password helloworld)
- Create logo
- Need help creating demo video
- Write docs using a tool like larecipe
- Write issue and contribution template file
etc





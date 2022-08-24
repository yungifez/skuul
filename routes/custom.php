<?php

/**
 * -------------------------------------------------------------------------------
 * Custom routes 
 * -------------------------------------------------------------------------------
 * 
 * These routes are user defined and this file would be rarely updated.
 * It can be used for tasks like routing when building your own frontend or 
 * adding additional pages to your sites and is intended as a way to allow smooth 
 * updates and prevent merge conflicts
 * 
 * Avoid using any route with "/dashoard" as a prefix as it may break the app
 */

Route::get('/', function () {
    return view('welcome');
});
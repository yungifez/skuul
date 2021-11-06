<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        return view('pages.school.index');
    }
}

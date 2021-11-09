<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        return view('pages.school.index');
    }

    public function show($id)
    {
        return view('pages.school.show');
    }

    public function create()
    {
        return view('pages.school.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function edit($id)
    {
        return view('pages.school.edit');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}

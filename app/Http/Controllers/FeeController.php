<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Fee::class);
    }

    public function index(): View
    {
        return view('pages.fee.index');
    }

    public function create(): View
    {
        return view('pages.fee.create');
    }

    public function show(Fee $fee): Response
    {
        abort(404);
    }

    public function edit(Fee $fee): View
    {
        return view('pages.fee.edit', compact('fee'));
    }
}

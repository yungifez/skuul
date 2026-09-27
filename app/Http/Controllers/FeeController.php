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

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.fee.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.fee.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fee $fee): View
    {
        return view('pages.fee.edit', compact('fee'));
    }
}

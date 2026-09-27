<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FeeCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FeeCategory::class, 'fee_category');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.fee.fee-category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.fee.fee-category.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(FeeCategory $feeCategory): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeeCategory $feeCategory): View
    {
        return view('pages.fee.fee-category.edit', compact('feeCategory'));
    }
}

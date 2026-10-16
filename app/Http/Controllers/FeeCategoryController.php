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

    public function index(): View
    {
        return view('pages.fee.fee-category.index');
    }

    public function create(): View
    {
        return view('pages.fee.fee-category.create');
    }

    public function show(FeeCategory $feeCategory): Response
    {
        abort(404);
    }

    public function edit(FeeCategory $feeCategory): View
    {
        return view('pages.fee.fee-category.edit', compact('feeCategory'));
    }
}

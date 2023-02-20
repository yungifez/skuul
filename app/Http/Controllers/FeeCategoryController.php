<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeCategoryRequest;
use App\Http\Requests\UpdateFeeCategoryRequest;
use App\Models\FeeCategory;
use App\Services\Fee\FeeCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FeeCategoryController extends Controller
{
    /**
     * Service instance of fee category.
     *
     * @var FeeCategoryService
     */
    public FeeCategoryService $feeCategoryService;

    public function __construct(FeeCategoryService $feeCategoryService)
    {
        $this->authorizeResource(FeeCategory::class, 'fee_category');
        $this->feeCategoryService = $feeCategoryService;
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
     * Store a newly created resource in storage.
     */
    public function store(StoreFeeCategoryRequest $request): RedirectResponse
    {
        $this->feeCategoryService->storeFeeCategory($request->validated());

        return back()->with('success', 'Fee Category Successfully Created');
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeeCategoryRequest $request, FeeCategory $feeCategory): RedirectResponse
    {
        $this->feeCategoryService->updateFeeCategory($feeCategory, $request->validated());

        return back()->with('success', 'Fee Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeCategory $feeCategory): RedirectResponse
    {
        $this->feeCategoryService->deleteFeeCategory($feeCategory);

        return back()->with('success', 'Fee Category Deleted Successfully');
    }
}

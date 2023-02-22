<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeRequest;
use App\Http\Requests\UpdateFeeRequest;
use App\Models\Fee;
use App\Services\Fee\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FeeController extends Controller
{
    public FeeService $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
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
     * Store a newly created resource in storage.
     */
    public function store(StoreFeeRequest $request): RedirectResponse
    {
        $this->feeService->storeFee($request->validated());

        return back()->with('success', 'Fee Created Successfully');
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeeRequest $request, Fee $fee): RedirectResponse
    {
        $this->feeService->updateFee($fee, $request->validated());

        return back()->with('success', 'Fee Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fee $fee): RedirectResponse
    {
        $this->feeService->deleteFee($fee);

        return back()->with('success', 'Fee Deleted Successfully');
    }
}

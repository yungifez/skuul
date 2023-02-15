<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;
use App\Models\Notice;
use App\Services\Notice\NoticeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public $notice;

    public function __construct(NoticeService $notice)
    {
        $this->authorizeResource(Notice::class, 'notice');
        $this->notice = $notice;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.notice.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.notice.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoticeRequest $request): RedirectResponse
    {
        $this->notice->storeNotice($request->except('_token'));

        return back()->with('success', 'Notice created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice): View
    {
        return view('pages.notice.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice): Response
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoticeRequest $request, Notice $notice): Response
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice): RedirectResponse
    {
        $this->notice->deleteNotice($notice);

        return back()->with('success', 'Notice deleted successfully');
    }
}

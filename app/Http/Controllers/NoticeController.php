<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Services\Notice\NoticeService;
use App\Http\Requests\StoreNoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;

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
     *
     * @param \App\Http\Requests\StoreNoticeRequest $request
     */
    public function store(StoreNoticeRequest $request): RedirectResponse
    {
        $this->notice->storeNotice($request->except('_token'));

        return back()->with('success', 'Notice created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Notice $notice
     */
    public function show(Notice $notice): View
    {
        return view('pages.notice.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Notice $notice
     */
    public function edit(Notice $notice): Response
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateNoticeRequest $request
     * @param \App\Models\Notice                     $notice
     */
    public function update(UpdateNoticeRequest $request, Notice $notice): Response
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Notice $notice
     */
    public function destroy(Notice $notice): RedirectResponse
    {
        $this->notice->deleteNotice($notice);

        return back()->with('success', 'Notice deleted successfully');
    }
}

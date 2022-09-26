<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;
use App\Models\Notice;
use App\Services\Notice\NoticeService;

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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.notice.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.notice.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreNoticeRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNoticeRequest $request)
    {
        $this->notice->storeNotice($request->except('_token'));

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Notice $notice)
    {
        return view('pages.notice.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Notice $notice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateNoticeRequest $request
     * @param \App\Models\Notice                     $notice
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNoticeRequest $request, Notice $notice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Notice $notice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notice $notice)
    {
        $this->notice->deleteNotice($notice);

        return back();
    }
}

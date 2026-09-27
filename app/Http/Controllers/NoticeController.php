<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Notice::class, 'notice');
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
     * Display the specified resource.
     */
    public function show(Notice $notice): View
    {
        return view('pages.notice.show', compact('notice'));
    }
}

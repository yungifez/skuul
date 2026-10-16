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

    public function index(): View
    {
        return view('pages.notice.index');
    }

    public function create(): View
    {
        return view('pages.notice.create');
    }

    public function show(Notice $notice): View
    {
        return view('pages.notice.show', compact('notice'));
    }
}

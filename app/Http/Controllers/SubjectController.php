<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Services\Subject\SubjectService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public $subject;

    public function __construct(SubjectService $subject)
    {
        $this->subject = $subject;
        $this->authorizeResource(Subject::class, 'subject');
    }

    public function index(): View
    {
        return view('pages.subject.index');
    }

    public function create(): View
    {
        return view('pages.subject.create');
    }

    public function show(Subject $subject): Response
    {
        abort(404);
    }

    public function edit(Subject $subject): View
    {
        $data['subject'] = $subject;

        return view('pages.subject.edit', $data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TranscriptSnapshot;
use Illuminate\View\View;

class TranscriptController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TranscriptSnapshot::class);

        return view('pages.transcript.index');
    }
}

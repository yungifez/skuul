<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$syllabus->name}}</h2>
    </div>
    <div class="card-body">
        <dl class="mb-5 grid gap-3 text-sm sm:grid-cols-3">
            <div><dt class="text-muted-foreground">Subject</dt><dd class="font-medium">{{ $syllabus->courseOffering->subject->name }}</dd></div>
            <div><dt class="text-muted-foreground">Academic level</dt><dd class="font-medium">{{ $syllabus->courseOffering->academicLevel->label ?? $syllabus->courseOffering->academicLevel->name }}</dd></div>
            <div><dt class="text-muted-foreground">Academic period</dt><dd class="font-medium">{{ $syllabus->courseOffering->academicPeriod->label ?? $syllabus->courseOffering->academicPeriod->name }}</dd></div>
        </dl>
        <p class="my-3">
            {{$syllabus->description}}
        </p>
        <p class="mb-4 text-sm text-muted-foreground">Revision {{ $syllabus->revision }} · {{ $syllabus->status->value }}</p>
        @can('update', $syllabus)
            @if ($syllabus->status === \App\Enums\SyllabusStatus::Published)
                <form method="POST" action="{{ route('syllabi.revise', $syllabus) }}" class="mb-3">
                    @csrf
                    <april:button type="submit" variant="outline">Create revised draft</april:button>
                </form>
            @elseif ($syllabus->status === \App\Enums\SyllabusStatus::Draft)
                <form method="POST" action="{{ route('syllabi.publish', $syllabus) }}" class="mb-3">
                    @csrf
                    <april:button type="submit">Publish revision</april:button>
                </form>
            @endif
        @endcan
        <a class="bg-blue-600 py-2 px-4 text-white rounded" href="{{asset('storage/'.$syllabus->file)}}" download>
            <x-lucide-download class="size-4"  />
            Download
        </a>
    </div>
</div>

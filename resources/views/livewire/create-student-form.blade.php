<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create Student</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" class="space-y-6" x-data="{ submitting: false }" x-on:submit="if (submitting) { $event.preventDefault(); } submitting = true">
            <livewire:create-user-fields role="Student" />
            @csrf
            <livewire:create-student-record-fields />
            <april:button type="submit" class="w-full md:w-auto" x-bind:disabled="submitting">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>

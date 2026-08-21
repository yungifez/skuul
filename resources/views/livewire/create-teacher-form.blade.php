<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create Teacher</h2>
    </div>
    <div class="card-body">
        <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
            <livewire:create-user-fields role="Teacher" />
            @csrf
            <april:button type="submit" class="w-full md:w-3/12">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create Teacher</h2>
    </div>
    <div class="card-body">
        <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
            <livewire:create-user-fields role="Teacher" />
            @csrf
            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-3/12"/>
            </div>
        </form>
    </div>
</div>

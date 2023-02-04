<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create Admin</h2>
    </div>
    <div class="card-body">
        <form action="{{route('admins.store')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
            <livewire:create-user-fields role="Admin" />
            @csrf
            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-3/12"/>
            </div>
        </form>
    </div>
</div>

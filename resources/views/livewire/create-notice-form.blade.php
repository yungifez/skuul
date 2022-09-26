<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Notice</h4>
    </div>
    <div class="card-body">
        <form action="{{route('notices.store')}}" method="post" enctype="multipart/form-data">
            @livewire('display-validation-error')
            <x-adminlte-input name="title" label="Notice title" placeholder="Enter Notice title" fgroup-class="col-md-6" enable-old-support/>
            <x-adminlte-textarea name="content" label="Notice content/body" placeholder="Enter body" fgroup-class="col-md-6" enable-old-support/>
            <div class="col-md-6">
                <x-adminlte-input-date name="start_date" label="Start date" required :config="['format' => 'YYYY/MM/DD']" enable-old-support/>
            </div>
            <div class="col-md-6">
                <x-adminlte-input-date name="stop_date" label="Stop date" required :config="['format' => 'YYYY/MM/DD']"  enable-old-support/>
            </div>
            @csrf
            <x-adminlte-input-file name="attachment" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,.pdf" label="Upload file" placeholder="Choose a file...(optional)" fgroup-class="col-md-6"/>
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>

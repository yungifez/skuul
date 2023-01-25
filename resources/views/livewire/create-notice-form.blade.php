<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Notice</h4>
    </div>
    <div class="card-body">
        <form action="{{route('notices.store')}}" method="post" enctype="multipart/form-data" class="md:w-1/2">
            <x-display-validation-errors/>
            <x-input id="title" name="title" label="Notice title" placeholder="Enter Notice title"  />
            <x-textarea id="content" name="content" label="Notice content/body" placeholder="Enter body"  />
            <x-input type="date" id="start_date" name="start_date" label="Start date" required />
            <x-input type="date" id="stop_Date" name="stop_date" label="Stop date"/>
            @csrf
            <x-input id="file" type="file" name="attachment" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,.pdf" label="Upload file" placeholder="Choose a file...(optional)" />
            <div class='col-12 my-2'>
                <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
            </div>
        </form>
    </div>
</div>

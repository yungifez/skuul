<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Notice</h4>
    </div>
    <div class="card-body">
        <form action="{{route('notices.store')}}" method="post" enctype="multipart/form-data" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="title" name="title" label="Notice title" placeholder="Enter Notice title" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="content">Notice content/body</april:label>
                <april:textarea id="content" name="content" placeholder="Enter body" />
            </div>
            <april:input-group type="date" id="start_date" name="start_date" label="Start date" required />
            <april:input-group type="date" id="stop_Date" name="stop_date" label="Stop date" />
            @csrf
            <april:input-group id="file" type="file" name="attachment" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,.pdf" label="Upload file" placeholder="Choose a file...(optional)" />
            <div class='col-12 my-2'>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
            </div>
        </form>
    </div>
</div>

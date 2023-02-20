<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit {{$feeCategory->name}}</h2>
    </div>
    <div class="card-body">
        <form action="{{route('fee-categories.update', $feeCategory->id)}}" class="md:w-6/12" method="POST">
            <x-display-validation-errors/>
            <x-input id="name" name="name" placeholder="Fee Category Name" label="Name" :value="$feeCategory->name"/>
            <x-textarea id="description" name="description" placeholder="Fee Category Description" label="description">
                {{$feeCategory->description}}
            </x-textarea>
            @method('PUT')
            @csrf
            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>

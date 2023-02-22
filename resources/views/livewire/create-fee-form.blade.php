<div class="card">
    <div class="card-header">
        <h2 class="card-titile">Create Fee</h2>
    </div>
    <div class="card-body">
        <form action="{{route('fees.store')}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Name" placeholder="Fee Name"/>
            <x-textarea id="description" name="description" placeholder="Fee Description" label="Description"/>
            <x-select id="fee-category" name="fee_category_id" label="Fee Category">
                @foreach ($feeCategories as $feeCategory)
                    <option value="{{$feeCategory->id}}">{{$feeCategory->name}}</option>
                @endforeach
            </x-select>
            @csrf
            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>
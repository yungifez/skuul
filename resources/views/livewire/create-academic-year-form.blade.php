<div class="card">
    <div class="card-body">
        <form action="{{route('academic-years.store')}}" autocomplete="off" method="POST" class="md:w-6/12">
                <x-display-validation-errors/>
                <x-input-year id="start-year" name="start_year" label="Start year" required />
                <x-input-year id="stop-year" name="stop_year" label="Stop year" required/>
            @csrf
            <x-button label="Create" icon="fas fa-key" type="submit" class="w-full md:w-6/12"/>
        </form>
    </div>
</div>

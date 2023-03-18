<div class="card">
    <div class="card-header">
        <h4 class="card-title">Notices</h4>
    </div>
    <div class="card-body">
        @can (['update notice', 'delete notice'])
            <livewire:datatable :model="App\Models\Notice::class" 
            uniqueId="List-notice-table"
            :filters="[
                ['name' => 'where', 'arguments' => ['school_id' , auth()->user()->school_id]]
            ]"
            :columns="[
               [ 'property' => 'title'],
               [ 'property' => 'start_date_for_humans', 'name' => 'Start Date', 'columnName' => 'start_date'],
               [ 'property' => 'stop_date_for_humans', 'name' => 'Stop Date',  'columnName' => 'stop_date'],
               ['name' => 'actions' , 'type' => 'dropdown' , 'links' => [
                    ['href' => 'notices.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
               ]],
               ['type' => 'delete' , 'name' => 'delete', 'action' => 'notices.destroy']
            ]"
            />
        @else
            <livewire:datatable :model="App\Models\Notice::class" 
            :filters="[
                ['name' => 'where', 'arguments' => ['school_id' , auth()->user()->school_id]],
                ['name' => 'active']
            ]"
            :columns="[
            [ 'property' => 'title'],
            ['name' => 'action' , 'type' => 'dropdown' , 'links' => [
                ['href' => 'notices.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
            ]],
            ]"
            />
        @endcan
    </div>
</div>

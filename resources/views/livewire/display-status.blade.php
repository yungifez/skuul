<div class="fixed-top d-flex flex-column align-items-end " style="z-index: 2000" id="status-display">
    @if (session('danger'))
        <x-adminlte-alert theme="danger" title="Danger" dismissable class="col-lg-4">
            {{ session('danger') }}
        </x-adminlte-alert>
    @endif
  
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable class="col-lg-4">
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
    @if (session('info'))
    <x-adminlte-alert theme="info" title="info" dismissable class="col-lg-4">
        {{ session('info') }}
    </x-adminlte-alert>
    @endif
    <div class="alert alert-danger alert-dismissible fade hide" role="alert" wire:offline.class="show" wire:offline.remove="hide">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">Close</span>
        </button>
        You are now offline. Please check your internet connection.
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('#status-display').remove()
        }, 10000);
    </script>
</div>



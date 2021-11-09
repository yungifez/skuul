<div class="fixed-top d-flex flex-column align-items-end " style="z-index: 2000">
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
</div>

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
    <script>
        setTimeout(() => {
            document.querySelector('#status-display').remove()
        }, 10000);
    </script>
</div>



<div>
    <div class="fixed flex flex-col items-end top-0 right-0 w-screen lg:w-4/12" id="status-display" x-data="{show: true }" x-inti="setTimeout(() => {show = false}, 10000)" x-show="show">
        @if (session('danger'))
            <x-alert colour="bg-red-500" title="Danger" icon="fa fa-do-ban">
                {{ session('danger') }}
            </x-alert>
        @endif
        @if (session('success'))
            <x-alert colour="bg-green-500" title="Success" icon="fa fa-check">
                {{ session('success') }}
            </x-alert>
        @endif
        @if (session('info'))
            <x-alert colour="bg-yellow-500" title="info">
                {{ session('info') }}
            </x-alert>
        @endif
    
    </div>
</div>
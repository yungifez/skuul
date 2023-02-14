<div>
    <div class="fixed flex flex-col items-end top-0 right-0 w-screen lg:w-4/12" id="status-display">
        @if (session('danger'))
            <x-alert colour="bg-red-500" title="Danger" icon="fa fa-do-ban" dismissOnTimeout="true" >
                {{ session('danger') }}
            </x-alert>
        @endif
        @if (session('success'))
            <x-alert colour="bg-green-500" title="Success" icon="fa fa-check" dismissOnTimeout="true" >
                {{ session('success') }}
            </x-alert>
        @endif
        @if (session('info'))
            <x-alert colour="bg-yellow-500" title="info" dismissOnTimeout="true">
                {{ session('info') }}
            </x-alert>
        @endif
        @if (session('status'))
            <x-alert colour="bg-green-500" title="Success" icon="fa fa-check" dismissOnTimeout="true" >
                {{ session('status') }}
            </x-alert>
        @endif
        <x-alert colour="bg-red-500" title="No Internet" :stack-icons="['fa fa-signal', 'fa fa-ban']" show="false">
            <div  @offline.window="showAlert = true" @online.window="showAlert = false">
                Your Device Has Gone Offline
            </div>
        </x-alert>
    </div>
</div>
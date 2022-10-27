<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class=" col-lg-10 my-4">
                <div class="col-md-7 m-auto ">
                    <x-jet-authentication-card-logo />
                </div>
                <div class="card shadow-sm px-1">
                    @livewire('user-registration-form')
                </div>
            </div>
        </div>
    </div>
    @livewire('display-status')
</x-guest-layout>
<div class="w-full" x-data="{open: true}" @click="open = false" x-show="open" x-transition>
    @if ($errors->any())
        <div class=" bg-red-100 text-red-900 border-red-300 border w-full">
            <div class="py-2 px-4 text-black flex justify-end">
                <button type="button">
                    <i class="fa fa-x" aria-hidden="true"></i>
                </button>
            </div>
            <ul class="pb-6 px-6">
                <p class="font-bold text-lg"> Whoops! Something went wrong.</p>
                @foreach ($errors->all() as $error)
                    <div class="pl-6">
                        <li class=" list-disc">{{ $error }}</li> 
                    </div>
                @endforeach
            </ul>
        </div>
    @endif
</div>
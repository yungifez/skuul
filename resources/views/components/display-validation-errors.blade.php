@props(['errorBag' => 'default'])

<div class="w-full" x-data="{open: true}" x-show="open" x-transition wire:ignore>
    @if ($errors->$errorBag->any())
        <div class=" bg-red-100 rounded-md dark:bg-red-600 dark:text-white text-red-900 border-red-300 border w-full">
            <div class="py-2 px-4 text-black dark:text-white flex justify-end">
                <button type="button"  @click="open = false">
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
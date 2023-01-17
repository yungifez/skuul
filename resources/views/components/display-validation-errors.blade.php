<div class="w-full">
    @if ($errors->any())
        <div class="p-6 bg-red-100 text-red-900 border-red-300 border w-full">
            <ul>
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
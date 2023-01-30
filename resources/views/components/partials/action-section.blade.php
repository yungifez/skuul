<div {{ $attributes->merge(['class' => 'md:grid grid-cols-12']) }}>
    <div class="md:col-span-4">
        <h2 class="text-xl font-bold my-2">{{$title}}</h2>
        <p class="text-sm">{{$description}}</p>
    </div>

    <div class="md:col-span-8">
        <div class="card shadow-sm">
            <div class="card-body">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
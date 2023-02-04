@props(['class' => ''])
<div class="flex flex-cols justify-center items-center my-12 flex-col">
    <img src="{{@asset(config('app.logo'))}}" alt="" class="rounded-full w-28 h-28 border border-gray-200 shadow-lg my-4">
    <div class="{{$class}} w-10/12 md:w-8/12 lg:w-7/12 xl:w-4/12 min-h-[20%] flex flex-col justify-center items-center bg-white shadow-lg rounded-lg p-3">
       {{$slot}}
    </div>
</div>

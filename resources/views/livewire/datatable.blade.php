<div>
    <x-loading-spinner/>
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="flex gap-4 items-center overflow-scroll beautify-scrollbar">
            <label for="datatable-search-{{$uniqueId}}">Search</label>
            <input id="datatable-search-{{$uniqueId}}" type="search" wire:model.live.sebounce.500ms="search" class="border-gray-500 dark:bg-inherit border rounded px-4 py-1 md:py-2">
        </div>
        <select class="bg-white dark:bg-gray-800 px-4 py-2 border border-gray-500 rounded" wire:model.live="perPage">
            @foreach ([5,10,20,25,100] as $item)
                <option value="{{$item}}" class="bg-inherit">{{$item}}</option>
            @endforeach
        </select>
    </div>
    <div class="overflow-x-scroll beautify-scrollbar" >
        <table class="border w-full my-4 table-auto">
            <thead class="border text-center bg-gray-900 dark:bg-white dark:bg-opacity-20 text-white">
                <th class="p-4">S/N</th>
                @foreach ($columns as $column)
                    @if (!isset($column['can']) || auth()->user()->can($column['can']))
                        <th class="capitalize p-4 border whitespace-nowrap">{{str_replace('_' , ' ', Str::snake( $column['name'] ??  $column['property']))}}</th>
                    @endif
                @endforeach
            </thead>
            <tbody class="">
                @if ($collection->isNotEmpty())
                    @foreach ($collection as $item)
                        <tr class="border odd:bg-white even:bg-slate-100 dark:odd:bg-inherit dark:even:bg-white dark:even:bg-opacity-5">
                            <th class="border w-24">{{ $collection->perPage() * ($collection->currentPage() - 1) + $loop->iteration }}</th>
                            @foreach ($columns as $column)
                                @if (!isset($column['can']) || auth()->user()->can($column['can']))
                                    <td class="p-4 border w-60 whitespace-nowrap">
                                        @php 
                                            $model = $item;
                                            if (isset($column['relation'])) {
                                                $relations = explode('.',$column['relation']);
                                                foreach ($relations as $relation){
                                                    $model = $model->$relation;
                                                }
                                            }
                                            if (is_array($model)) {
                                                $model = collect($model);
                                            }
                                            
                                        @endphp
                                        <p class="{{$column['class'] ?? null}}">
                                            @if (array_key_exists('method', $column) && !empty($column['method']))
                                                {{ ($model?->{$column['method']}()) }}
                                            @elseif (array_key_exists('type', $column) && !empty($column['type']))
                                                @if ($column['type'] == 'delete')
                                                    <x-modal title="Confirm {{$column['name']}}" background-colour="bg-red-600">
                                                        <div class="text-gray-700 text-center dark:text-white">
                                                            <i class="fa fa-trash text-7xl" aria-hidden="true"></i>
                                                            <p class="my-2">Are you sure you want to {{Str::lower($column['name'])}} this resource</p>
                                                        </div>
                                                        <x-slot:footer>
                                                            <form action="{{route($column['action'],array_merge(($column['pre-route-parameters'] ?? []),[$model->id], ($column['post-route-parameters'] ?? [])))}}" method="POST">
                                                                <x-button class="bg-red-600" icon="fa fa-trash" >
                                                                    Continue with {{Str::lower($column['name'])}}
                                                                </x-button>
                                                                @method('delete')
                                                                @csrf
                                                            </form>
                                                        </x-slot:footer>
                                                    </x-modal>
                                                @elseif ($column['type'] == 'dropdown')
                                                    <x-dropdown >
                                                        @foreach ($column['links'] as $link)
                                                            @if (!isset($link['can']) || auth()->user()->can($link['can']))
                                                                <a href="{{route($link['href'],array_merge(($link['pre-route-parameters'] ?? []),[$model->id], ($link['post-route-parameters'] ?? [])))}}" class="flex capitalize items-center justify-start gap-2 py-3 px-6 hover:bg-white hover:bg-opacity-20 "><i class="{{$link['icon'] ?? ''}}" aria-hidden="true"></i>{{$link['text']}}</a>
                                                            @endif
                                                        @endforeach
                                                    </x-dropdown>
                                                @elseif($column['type'] == 'boolean-switch')
                                                <form action="{{route($column['action'], $model->id)}}" method="POST" x-data>
                                                    @csrf
                                                    <x-toggle :name="$column['field']" :checked="$model?->{$column['property'] ?? $column['name']}  == true"  :label-checked-text="$column['true-statement'] ?? 'yes'" :label-unchecked-text="$column['false-statement']?? 'no'" @Change="$nextTick(() => $el.form.submit())"/>
                                                </form>
                                                @elseif($column['type'] == 'image')
                                                    <div class="flex justify-center">
                                                        <img class="{{$column['img-class'] ?? " h-14 w-1/2 rounded-full"}}" loading="lazy" src="{{($model?->{$column['property'] ?? $column['name']}) }}" alt="">
                                                    </div>
                                                @endif
                                            @else
                                                @php
                                                    $property = ($model?->{$column['property'] ?? $column['name']})
                                                @endphp
                                                @if ($property instanceof \Carbon\Carbon)
                                                    {{$property->format('Y/m/d')}}
                                                @elseif($property instanceof \Brick\Money\Money)
                                                    {{$property->formatTo(app()->getLocale())}}
                                                @else
                                                    {{$property}}
                                                @endif
                                            @endif
                                        </p>
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="p-4 capitalize text-center" colspan="100%">No data to Show</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="my-3">
        {{$collection->links()}}
    </div>
</div>

<div>
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="flex gap-4 items-center">
            <label>Search</label>
            <input type="search" wire:model.sebounce.500ms="search" class="dark:bg-inherit border rounded px-4 py-1 md:py-2">
        </div>
        <select class="dark:bg-gray-800 px-4 py-2 border rounded" wire:model="perPage">
            @foreach ([5,10,20,25,100] as $item)
                <option value="{{$item}}" class="bg-inherit">{{$item}}</option>
            @endforeach
        </select>
    </div>
    <div class="overflow-x-scroll beautify-scrollbar text-center">
        <table class="border w-full my-4 table-auto">
            <thead class="border bg-gray-900 dark:bg-white dark:bg-opacity-20 text-white">
                <th class="p-4">S/N</th>
                @foreach ($columns as $column)
                    <th class="capitalize p-4 border whitespace-nowrap">{{str_replace('_' , ' ', Str::snake( $column['name'] ??  $column['property']))}}</th>
                @endforeach
            </thead>
            <tbody class="">
                @foreach ($collection as $item)
                <tr class="border odd:bg-white even:bg-slate-100 dark:odd:bg-inherit dark:even:bg-white dark:even:bg-opacity-5">
                        <th class="border w-24">{{ $collection->perPage() * ($collection->currentPage() - 1) + $loop->iteration }}</th>
                        @foreach ($columns as $column)
                            <td class="p-4 px-4 border w-60 whitespace-nowrap">
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
                                <p>
                                    @if (array_key_exists('method', $column) && !empty($column['method']))
                                        {{ ($model?->{$column['method']}()) }}
                                    @elseif (array_key_exists('type', $column) && !empty($column['type']))
                                        @if ($column['type'] == 'delete')
                                            <x-modal title="Confirm {{$column['name']}}" background-colour="bg-red-600">
                                                <div class="text-gray-700 dark:text-white">
                                                    <i class="fa fa-trash  text-7xl" aria-hidden="true"></i>
                                                    <p class="my-2">Are you sure you want to {{Str::lower($column['name'])}} this resource</p>
                                                </div>
                                                <x-slot:footer>
                                                    <form action="{{route($column['action'], $model->id)}}" method="POST">
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
                                                    <a href="{{route($link['href'], $model->id)}}" class="flex items-center justify-start gap-2 py-4 px-6 hover:bg-white hover:bg-opacity-20 "><i class="{{$link['icon'] ?? ''}}" aria-hidden="true"></i>{{$link['text']}}</a>
                                                @endforeach
                                            </x-dropdown>
                                        @endif
                                    @else
                                        {{ ($model?->{$column['property'] ?? $column['name']}) }}
                                    @endif
                                </p>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="my-3">
        {{$collection->links()}}
    </div>
</div>

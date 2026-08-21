<div>
    <x-loading-spinner/>
    <div class="flex flex-col items-stretch gap-3 rounded-lg border bg-card p-4 text-card-foreground shadow-sm md:flex-row md:items-end">
        <div class="flex flex-1 flex-col gap-2">
            <april:label for="datatable-search-{{$uniqueId}}">Search</april:label>
            <april:input id="datatable-search-{{$uniqueId}}" type="search" wire:model.live.debounce.500ms="search" placeholder="Search..." />
        </div>
        <april:native-select class="w-full md:w-32" wire:model.live="perPage" aria-label="Rows per page">
            @foreach ([5,10,20,25,100] as $item)
                <option value="{{$item}}">{{$item}}</option>
            @endforeach
        </april:native-select>
    </div>
    <div class="my-4 overflow-x-auto rounded-lg border bg-card text-card-foreground beautify-scrollbar">
        <table class="w-full table-auto text-sm">
            <thead class="border-b bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                <th class="whitespace-nowrap px-4 py-3 font-medium">S/N</th>
                @foreach ($columns as $column)
                    @if (!isset($column['can']) || auth()->user()->can($column['can']))
                        <th class="whitespace-nowrap border-l px-4 py-3 font-medium capitalize">{{str_replace('_' , ' ', Str::snake( $column['name'] ??  $column['property']))}}</th>
                    @endif
                @endforeach
            </thead>
            <tbody class="divide-y">
                @if ($collection->isNotEmpty())
                    @foreach ($collection as $item)
                        <tr class="transition-colors hover:bg-muted/50">
                            <th class="w-24 px-4 py-3 text-left font-medium">{{ $collection->perPage() * ($collection->currentPage() - 1) + $loop->iteration }}</th>
                            @foreach ($columns as $column)
                                @if (!isset($column['can']) || auth()->user()->can($column['can']))
                                    <td class="w-60 whitespace-nowrap border-l px-4 py-3">
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
                                                @elseif($column['type'] == 'account-status')
                                                    <x-account-status-control :user="$model" />
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
                                                    {{$property->formatToLocale(app()->getLocale())}}
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
                        <td class="px-4 py-10 text-center capitalize text-muted-foreground" colspan="100%">No data to Show</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="my-3 flex justify-end">
        {{$collection->links()}}
    </div>
</div>

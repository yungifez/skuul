@props(['head' => ['key' , 'value'], 'body' => [['value' , 'another value'], ['value', 'another value']]])

<div class="overflow-scroll beautify-scrollbar w-full ">
    <table class="w-full border-gray-800 border dark:border-white">
        @isset($head)
            <thead>
                <tr>
                    @foreach ($head as $th)
                        <th class="bold p-4 border border-gray-800 dark:border-white text-left capitalize">{{$th}}</th>
                    @endforeach
                </tr>
                <tbody>
                    @foreach ($body as $item)
                        <tr>
                            @foreach ($item as $value)
                                <td class="border-gray-800 border dark:border-white w-[50%] p-4 whitespace-nowrap">{{$value}}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </thead>
        @endisset
    </table>
</div>
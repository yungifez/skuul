<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DropdownLinks extends Component
{
    public $button_label = 'actions';

    /**
     * Array containing hrefs, texts and icons.
     *
     * @var array
     */
    public array $links;

    public $forms;

    public function __construct($links)
    {
        $this->links = $links;
    }

    public function render()
    {
        return <<<'blade'
        <div class="dropdown">
            <button class="btn btn-secondary" data-toggle="dropdown" id="DropDownButton"  class="text-capitalize"  aria-haspopup="true" aria-expanded="false">
                {{$button_label ?? 'actions'}}
            </button>
            <div class="dropdown-menu" aria-labelledby="DropDownButton">
                @if (isset($links))
                    @foreach ($links as $link)
                        <a href="{{$link['href']}}" class="dropdown-item text-capitalize">
                            <i class="{{$link['icon']}} pr-2"></i>
                            {{$link['text']}}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
        blade;
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $class;

    public $icon;

    public $label;

    public $colour;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($class = null, $icon = null, $label = null, $colour = 'text-white  bg-blue-600')
    {
        $this->class = $class;
        $this->icon = $icon;
        $this->label = $label;
        $this->colour = $colour;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button');
    }
}

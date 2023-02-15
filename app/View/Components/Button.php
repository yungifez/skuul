<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $class;

    public $icon;

    public $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($class = null, $icon = null, $label = null)
    {
        $this->class = $class;
        $this->icon = $icon;
        $this->label = $label;
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

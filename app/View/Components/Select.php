<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{

    public string $label;
    public string $id;
    public string $name;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $id, $name)
    {
        $this->id = $id;
        $this->label = $label;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.select');
    }
}

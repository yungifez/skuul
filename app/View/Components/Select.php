<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{

    public ?string $label;
    public string $id;
    public string $name;
    public ?string $class;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $name, $class = null, string $label = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->name = $name;
        $this->class = $class;
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

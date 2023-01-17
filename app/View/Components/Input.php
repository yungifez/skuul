<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public string $id, $type, $name;
    public ?string $label, $class, $groupClass, $labelClass;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $id,string $type,string $name, string $label = null,string $class = null,string $groupClass = null, $labelClass = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->class = $class;
        $this->groupClass = $groupClass;
        $this->labelClass = $labelClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}

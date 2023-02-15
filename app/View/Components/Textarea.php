<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Textarea extends Component
{
    public string $id;

    public string $name;

    public ?string $label;

    public ?string $class;

    public ?string $groupClass;

    public ?string $labelClass;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $name, $label = null, $class = null, $groupClass = null, $labelClass = null)
    {
        $this->id = $id;
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
        return view('components.textarea');
    }
}

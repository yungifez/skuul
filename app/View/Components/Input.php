<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public string $id;

    public string $name;

    public ?string $label;

    public ?string $class;

    public ?string $groupClass;

    public ?string $labelClass;

    public ?string $value;

    public ?string $errorBag;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $id, string $name, $value = null, string $label = null, string $class = null, string $groupClass = null, string $labelClass = null, string $errorBag = 'default')
    {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->class = $class;
        $this->groupClass = $groupClass;
        $this->labelClass = $labelClass;
        $this->value = $value;
        $this->errorBag = $errorBag;
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

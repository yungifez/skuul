<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InfoBox extends Component
{
    public $icon, $title, $text, $url, $urlText, $colour, $class, $textColour;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $title,string $text, $colour = null, $icon = null, $class = null, $url = null, $urlText = null, $textColour = null)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->class = $class;
        $this->colour = $colour;
        $this->text = $text;
        $this->url = $url;
        $this->urlText = $urlText;
        $this->textColour = $textColour;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.info-box');
    }
}

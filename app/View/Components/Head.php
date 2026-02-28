<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Head extends Component
{
    public $title;
    public $description;

    public function __construct($title = null, $description = null)
    {
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        // Return the view for the component
        return view('components.head');
    }
}

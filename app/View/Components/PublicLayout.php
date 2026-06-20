<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PublicLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     * Used for citizen-facing (public) pages that do NOT require authentication.
     */
    public function render(): View
    {
        return view('layouts.public');
    }
}

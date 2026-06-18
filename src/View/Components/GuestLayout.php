<?php

namespace SistemasEel\PortalUi\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public ?string $title;

    public function __construct(?string $title = null)
    {
        $this->title = $title;
    }

    public function render(): View
    {
        return view('portal-ui::layouts.guest');
    }
}

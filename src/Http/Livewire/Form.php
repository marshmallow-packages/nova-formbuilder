<?php

namespace Marshmallow\NovaFormbuilder\Http\Livewire;

use Livewire\Component;
use Marshmallow\NovaFormbuilder\Http\Livewire\Traits\WithForm;

class Form extends Component
{
    use WithForm;

    public function render()
    {
        return view('nova-formbuilder::livewire.form');
    }
}

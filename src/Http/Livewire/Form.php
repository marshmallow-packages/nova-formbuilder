<?php

namespace Marshmallow\NovaFormbuilder\Http\Livewire\Forms;

use Livewire\Component;
use Marshmallow\NovaFormbuilder\Http\Livewire\Traits\WithForm;

class Form extends Component
{
    use WithForm;

    public function render()
    {
        return view('livewire.form');
    }
}

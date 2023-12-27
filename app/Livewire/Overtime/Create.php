<?php

namespace App\Livewire\Overtime;

use App\Livewire\Forms\OvertimeForm;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public OvertimeForm $form;

    public function mount()
    {
        $this->name = \Auth::user()->name;
    }

    public function render()
    {
        return view('livewire.overtime.create');
    }

    public function submit()
    {
        // TODO: get id in a smarter way, because admin can change ID for user
        $this->form->create(\Auth::id());
    }

    public function saveDraft()
    {
        // TODO: get id in a smarter way, because admin can change ID for user
        $this->form->create(\Auth::id(), isApplied: false);
    }
}

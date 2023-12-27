<?php

namespace App\Livewire\Overtime;

use App\Livewire\Forms\OvertimeForm;
use App\Models\Overtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public bool $locked = false;

    public OvertimeForm $form;

    public function mount(Request $request)
    {
        $this->name = \Auth::user()->name;

        try {
            $date = Carbon::parse($request->get('date'));
            $this->locked = $this->form->fromDate(\Auth::id(), $date->format('Y-m-d'));
        } catch (\Throwable $e) {

        }
    }

    public function render()
    {
        return view('livewire.overtime.create');
    }

    public function submit()
    {
        // TODO: get id in a smarter way, because admin can change ID for user (applicant_user_id)
        $this->form->save(\Auth::id());
    }

    public function saveDraft()
    {
        // TODO: get id in a smarter way, because admin can change ID for user (applicant_user_id)
        $this->form->save(\Auth::id(), isApplied: false);
    }

    public function updated($property, $value)
    {
        if ($property === 'form.date') {
            // TODO: get id in a smarter way, because admin can change ID for user (applicant_user_id)
            $this->formFromDate($value);
        }
    }

    private function formFromDate($date)
    {
        $this->locked = $this->form->fromDate(\Auth::id(), $date);
    }
}

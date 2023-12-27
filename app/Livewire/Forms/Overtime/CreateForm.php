<?php

namespace App\Livewire\Forms\Overtime;

use Livewire\Attributes\Rule;
use Livewire\Form;

class CreateForm extends Form
{
    #[Rule(['required', 'integer'])]
    public $user_id = '';

    #[Rule(['required', 'integer'])]
    public $overtime_confirmation_id = '';

    #[Rule(['required', 'date'])]
    public $date = '';

    #[Rule(['required', 'integer'])]
    public $from_hours = '';

    #[Rule(['required', 'integer'])]
    public $from_minutes = '';

    #[Rule(['required', 'integer'])]
    public $to_hours = '';

    #[Rule(['required', 'integer'])]
    public $to_minutes = '';

    #[Rule(['required'])]
    public $reason = '';

    #[Rule(['nullable'])]
    public $remarks = '';
}

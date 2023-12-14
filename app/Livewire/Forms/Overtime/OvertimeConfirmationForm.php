<?php

namespace App\Livewire\Forms\Overtime;

use App\Models\OvertimeConfirmation;
use Livewire\Attributes\Rule;
use Livewire\Form;

class OvertimeConfirmationForm extends Form
{
    #[Rule(['required', 'integer'])]
    public int $user_id = -1;

    #[Rule(['required', 'integer'])]
    public int $year = -1;

    #[Rule(['required', 'integer'])]
    public int $month = -1;

    #[Rule(['nullable', 'string'])]
    public ?string $remarks = null;

    #[Rule(['nullable', 'string'])]
    public ?string $transfer_remarks = null;

    #[Rule(['nullable', 'date'])]
    public ?string $confirmed_at = null;

    public function setForm(OvertimeConfirmation $overtimeConfirmation)
    {
        $this->user_id = $overtimeConfirmation->user_id;
        $this->year = $overtimeConfirmation->year;
        $this->month = $overtimeConfirmation->month;
        $this->remarks = $overtimeConfirmation->remarks;
        $this->transfer_remarks = $overtimeConfirmation->transfer_remarks;
        $this->confirmed_at = $overtimeConfirmation->confirmed_at;
    }
}

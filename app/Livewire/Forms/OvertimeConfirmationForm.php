<?php

namespace App\Livewire\Forms;

use App\Models\OvertimeConfirmation;
use Livewire\Form;

class OvertimeConfirmationForm extends Form
{
    public string $remarks = '';

    public string $transfer_remarks = '';

    public string $confirmed_at = '';

    public function setForm(OvertimeConfirmation $overtimeConfirmation)
    {
        $this->remarks = $overtimeConfirmation->remarks ?? '';
        $this->transfer_remarks = $overtimeConfirmation->transfer_remarks ?? '';
        $this->confirmed_at = $overtimeConfirmation->confirmed_at?->format('Y年m月d日 H:i') ?? '';
    }

    public function save(int $userId, int $year, int $month)
    {
        $confirmedAt = ($this->confirmed_at !== null && $this->confirmed_at !== '') ? $this->confirmed_at : now();

        OvertimeConfirmation::updateOrCreate([
            'user_id' => $userId,
            'year' => $year,
            'month' => $month,
        ], [
            'remarks' => $this->remarks,
            'transfer_remarks' => $this->transfer_remarks,
            'confirmed_at' => $confirmedAt,
        ]);
    }
}

<?php

namespace App\Livewire\Overtime;

use App\Livewire\Forms\Overtime\OvertimeConfirmationForm;
use App\Models\OvertimeConfirmation;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    public string $name = '';

    public OvertimeConfirmationForm $form;

    #[Url]
    public int $year;

    #[Url]
    public int $month;

    public function mount()
    {
        if (!isset($this->year)) {
            $this->year = now()->year;
        }

        if ($this->year > now()->year) {
            $this->year = now()->year;
        }

        if (!isset($this->month)) {
            $this->month = now()->month;
        }

        if ($this->month > 12 || $this->month < 1) {
            $this->month = 1;
        }

        $selectedDate = Carbon::parse($this->year . '-' . $this->month);
        if ($selectedDate->gt(now()->startOfMonth())) {
            $this->year = now()->year;
            $this->month = now()->month;
        }

        $this->name = \Auth::user()->name;
    }

    public function render()
    {
        $user = \Auth::user();

        $previousMonthOvertimeConfirmation = OvertimeConfirmation::where([
            'user_id' => $user->id,
            'year' => $this->previousMonth()->year,
            'month' => $this->previousMonth()->month,
        ])->first();

        $overtimeConfirmation = OvertimeConfirmation::firstOrNew([
            'user_id' => $user->id,
            'year' => $this->year,
            'month' => $this->month,
        ], [
            'remarks' => $previousMonthOvertimeConfirmation?->transfer_remarks,
        ]);

        $this->form->setForm($overtimeConfirmation);

        return view('livewire.overtime.index');
    }

    public function decreaseYear(): void
    {
        $this->year--;
    }

    public function increaseYear(): void
    {
        if ($this->year < now()->year) {
            $this->year++;
        }
    }

    public function decreaseMonth(): void
    {
        $date = Carbon::parse($this->year . '-' . $this->month)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function increaseMonth(): void
    {
        $date = Carbon::parse($this->year . '-' . $this->month)->addMonth();
        if ($date->startOfMonth() > now()->startOfMonth()) {
            return;
        }

        $this->year = $date->year;
        $this->month = $date->month;
    }

    #[Computed]
    public function previousMonth(): Carbon
    {
        return Carbon::parse($this->year . '-' . $this->month)->startOfMonth()->subMonth();
    }

    public function queryString()
    {
        return [
            'year' => ['except' => now()->year],
            'month' => ['except' => now()->month],
        ];
    }
}

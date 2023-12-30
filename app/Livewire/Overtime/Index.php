<?php

namespace App\Livewire\Overtime;

use App\Livewire\Forms\OvertimeConfirmationForm;
use App\Models\Overtime;
use App\Models\OvertimeConfirmation;
use Auth;
use Carbon\Carbon;
use IntlDateFormatter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    public string $name = '';

    #[Url]
    public int $year;

    #[Url]
    public int $month;

    public array $overtimeConfirmationsPerYear = [];

    public OvertimeConfirmationForm $form;

    public string $uuid = '';

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

        $this->name = Auth::user()->name;

        $this->overtimeConfirmationsPerYear = OvertimeConfirmation::query()
            ->select(['year', 'month'])
            ->where(['user_id' => Auth::user()->id])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->groupBy('year')
            ->toArray();
    }

    public function render()
    {
        // TODO: ADMIN CAN SELECT DIFFERENT USERS
        $user = Auth::user();

        $previousMonthOvertimeConfirmation = OvertimeConfirmation::where([
            'user_id' => $user->id,
            'year' => $this->previousMonth()->year,
            'month' => $this->previousMonth()->month,
        ])->first();

        $overtimeConfirmation = OvertimeConfirmation::query()
            ->firstOrNew([
                'user_id' => $user->id,
                'year' => $this->year,
                'month' => $this->month,
            ], [
                'remarks' => $previousMonthOvertimeConfirmation?->transfer_remarks,
            ]);

        $overtimes = collect();
        if ($overtimeConfirmation !== null) {
            $overtimes = Overtime::query()
                ->with(['creator', 'applicant', 'approver'])
                ->where('overtime_confirmation_id', '=', $overtimeConfirmation->id)
                ->orderBy('date', 'DESC')
                ->get()
                ->groupBy(function(Overtime $overtime): string {
                    if ($overtime->approved_at !== null) {
                        return 'approved';
                    }
                    if ($overtime->applied_at !== null) {
                        return 'applied';
                    }

                    return 'saved';
                });

            $this->uuid = $overtimeConfirmation->uuid ?? '';
        } else {
            $this->uuid = '';
        }

        $this->form->setForm($overtimeConfirmation);

        return view('livewire.overtime.index', [
            'overtimes' => $overtimes,
            'user' => Auth::user(),
        ]);
    }

    #[Computed]
    public function previousMonth(): Carbon
    {
        return Carbon::parse($this->year . '-' . $this->month)->startOfMonth()->subMonth();
    }

    public function submit()
    {
        $this->form->save(Auth::id(), $this->year, $this->month);
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

        $date = Carbon::parse($this->year . '-' . $this->month);
        if ($date->startOfMonth() > now()->startOfMonth()) {
            $this->month = now()->month;
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

    public function queryString()
    {
        return [
            'year' => ['except' => now()->year],
            'month' => ['except' => now()->month],
        ];
    }

    #[Computed]
    public function japaneseYear(): string
    {
        $formatter = new IntlDateFormatter(
            'ja_JP@calendar=japanese', IntlDateFormatter::FULL,
            IntlDateFormatter::NONE, 'Asia/Tokyo', IntlDateFormatter::TRADITIONAL
        );

        $dateString = $formatter->format(Carbon::parse($this->year . '-' . $this->month));

        $dateString = substr($dateString, 0, strpos($dateString, '年'));

        return $dateString;
    }

    #[Computed]
    public function hasCurrentYear(): bool
    {
        return count(array_filter($this->overtimeConfirmationsPerYear, function($item) {
            return $item['year'] === now()->year && $item['month'] === now()->month;
        })) > 0;
    }
}

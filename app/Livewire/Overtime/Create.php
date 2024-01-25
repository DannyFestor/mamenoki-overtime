<?php

namespace App\Livewire\Overtime;

use App\Livewire\Forms\OvertimeForm;
use App\Models\OvertimeConfirmation;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Component;
use Throwable;

class Create extends Component
{
    public string $name = '';

    public bool $isApproved = false;

    public bool $isConfirmed = false;

    public OvertimeForm $form;

    public $date = '';

    public function mount(Request $request)
    {
        $this->name = Auth::user()->name;

        try {
            $this->date = Carbon::parse($this->date)->format('Y-m-d');
        } catch (Throwable $e) {
            $this->date = now()->format('Y-m-d');
        } finally {
            $this->formFromDate();
        }
    }

    private function formFromDate(): void
    {
        $this->form->fromDate(Auth::id(), $this->date);
        $this->checkShouldLock($this->date);
    }

    /**
     * @param  Carbon  $dateObject
     */
    public function checkShouldLock(string $date): void
    {
        $this->isApproved = $this->form
            ->fromDate(Auth::id(), $date);

        [$year, $month, $day] = explode('-', $date);

        $this->isConfirmed = OvertimeConfirmation::query()
            ->where('user_id', '=', Auth::id())
            ->where('year', '=', $year)
            ->where('month', '=', $month)
            ->whereNotNull('confirmed_at')
            ->exists();
    }

    public function render(): View
    {
        return view('livewire.overtime.create');
    }

    public function updatedDate($value)
    {
        try {
            $this->date = Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable $e) {
            $this->date = now()->format('Y-m-d');
        } finally {
            $this->formFromDate();
        }
    }

    public function submit()
    {
        if ($this->isConfirmed || $this->isApproved) {
            dd();

            return;
        }

        // TODO: get id in a smarter way, because admin can change ID for user (applicant_user_id)
        $this->form->save(Auth::id());
    }

    public function saveDraft()
    {
        if ($this->isConfirmed || $this->isApproved) {
            dd();

            return;
        }

        // TODO: get id in a smarter way, because admin can change ID for user (applicant_user_id)
        $this->form->save(Auth::id(), isApplied: false);
    }

    protected function queryString()
    {
        return [
            'date' => [
                'except' => now()->format('Y-m-d'),
            ],
        ];
    }
}

<?php

namespace App\Livewire\Overtime;

use App\Models\OvertimeConfirmation;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use IntlDateFormatter;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public OvertimeConfirmation $overtime_confirmation;

    public User $user;

    public function mount(OvertimeConfirmation $overtime_confirmation)
    {
        $overtime_confirmation->load(['overtimes' => fn(HasMany $query) => $query
            ->whereNotNull('applied_at')
            ->whereNotNull('approved_at'),
        ]);
        $this->overtime_confirmation = $overtime_confirmation;

        $user = Auth::user();
        $user->load('userWorkInformation');
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.overtime.show');
    }

    #[Computed]
    public function japaneseYear(): string
    {
        $formatter = new IntlDateFormatter(
            'ja_JP@calendar=japanese', IntlDateFormatter::FULL,
            IntlDateFormatter::NONE, 'Asia/Tokyo', IntlDateFormatter::TRADITIONAL
        );

        $dateString = $formatter->format(Carbon::parse($this->overtime_confirmation->year . '-' . $this->overtime_confirmation->month));

        $dateString = substr($dateString, 0, strpos($dateString, '年'));

        return $dateString;
    }
}

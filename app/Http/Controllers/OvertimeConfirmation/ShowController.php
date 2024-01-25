<?php

namespace App\Http\Controllers\OvertimeConfirmation;

use App\Http\Controllers\Controller;
use App\Models\OvertimeConfirmation;
use Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Str;

class ShowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, OvertimeConfirmation $overtimeConfirmation)
    {
        $overtimeConfirmation->load(['overtimes' => fn(HasMany $query) => $query
            ->whereNotNull('applied_at')
            ->whereNotNull('approved_at'),
        ]);
        $this->overtime_confirmation = $overtimeConfirmation;

        $user = Auth::user();
        $user->load('userWorkInformation');

        $html = view('overtime_confirmation.show', [
            'overtimeConfirmation' => $overtimeConfirmation,
            'user' => $user,
        ])->render();

        $pdf = Browsershot::html($html)
            ->showBackground()
            ->margins(10, 10, 10, 10)
            ->pdf();

        $filename = $overtimeConfirmation->year . '-' . Str::padLeft($overtimeConfirmation->month, 2, '0') . '-' . $user->name . '.pdf';

        return response()->make($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}

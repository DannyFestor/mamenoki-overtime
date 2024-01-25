<?php

use App\Enums\OvertimeReason;
use App\Livewire\Overtime\Create;
use App\Models\Overtime;
use App\Models\OvertimeConfirmation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can see the page', function() {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/overtime/create')
        ->assertSeeLivewire(Create::class);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->assertSee($user->name)
        ->assertSet('isApproved', false)
        ->assertSet('isConfirmed', false)
        ->assertSet('name', Auth::user()->name)
        ->assertSet('date', now()->format('Y-m-d'));
});

test('a date can be set', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->withQueryParams(['date' => now()->subDays(5)->format('Y-m-d')])
        ->test(Create::class)
        ->assertSet('date', now()->subDays(5)->format('Y-m-d'));
});

test('an overtime can be saved', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.timeFrom', '12:00')
        ->set('form.timeUntil', '12:15')
        ->set('form.reason', OvertimeReason::ELSE->value)
        ->set('form.remarks', 'test1')
        ->call('saveDraft')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('overtimes', [
        'created_user_id' => $user->id,
        'date' => now()->format('Y-m-d'),
        'time_from' => '12:00:00',
        'time_until' => '12:15:00',
        'reason' => OvertimeReason::ELSE->value,
        'remarks' => 'test1',
        'applicant_user_id' => $user->id,
    ]);

    $overtime = Overtime::where([
        'created_user_id' => $user->id,
        'date' => now()->format('Y-m-d'),
        'time_from' => '12:00:00',
        'time_until' => '12:15:00',
        'reason' => OvertimeReason::ELSE->value,
        'remarks' => 'test1',
        'applicant_user_id' => $user->id,
    ])->first();

    $this->assertDatabaseHas('overtime_confirmations', [
        'id' => $overtime->overtime_confirmation_id,
        'user_id' => $user->id,
        'year' => now()->year,
        'month' => now()->month,
        'confirmed_at' => null,
    ]);

    Livewire::actingAs($user)
        ->withQueryParams(['date' => now()->subDay()->format('Y-m-d')])
        ->test(Create::class)
        ->set('form.timeFrom', '12:00')
        ->set('form.timeUntil', '12:15')
        ->set('form.reason', OvertimeReason::EXTENDED_CARE->value)
        ->set('form.remarks', 'test2')
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('overtimes', [
        'date' => now()->subDay()->format('Y-m-d'),
        'time_from' => '12:00:00',
        'time_until' => '12:15:00',
        'reason' => OvertimeReason::EXTENDED_CARE->value,
        'remarks' => 'test2',
        'created_user_id' => $user->id,
        'applicant_user_id' => $user->id,
        'applied_at' => now(),
    ]);
});

test('an overtime is loaded when the date is changed', function() {
    $user = User::factory()->create();

    $overtimeDate = now()->subDays(10);
    $overtimeConfirmation = OvertimeConfirmation::factory()->create([
        'user_id' => $user->id,
        'year' => $overtimeDate->year,
        'month' => $overtimeDate->month,
    ]);
    $overtime = Overtime::factory()->create([
        'created_user_id' => $user->id,
        'date' => $overtimeDate->format('Y-m-d'),
        'overtime_confirmation_id' => $overtimeConfirmation->id,
        'time_from' => '12:00',
        'time_until' => '12:00',
    ]);

    Livewire::actingAs($user)
        ->withQueryParams(['date' => $overtimeDate->format('Y-m-d')])
        ->test(Create::class)
        ->assertSet('form.reason', $overtime->reason->value);

    Livewire::actingAs($user)
        ->withQueryParams(['date' => now()->format('Y-m-d')])
        ->test(Create::class)
        ->set('date', $overtimeDate->format('Y-m-d'))
        ->assertSet('form.reason', $overtime->reason->value);
});

test('a date must be valid', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.date', '')
        ->call('submit')
        ->assertHasErrors(['form.date'])
        ->call('saveDraft')
        ->assertHasErrors(['form.date']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.date', 'invalid date')
        ->call('submit')
        ->assertHasErrors(['form.date'])
        ->call('saveDraft')
        ->assertHasErrors(['form.date']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.date', '01-01-2015')
        ->call('submit')
        ->assertHasErrors(['form.date'])
        ->call('saveDraft')
        ->assertHasErrors(['form.date']);
});

test('a timeFrom must be valid', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.timeFrom', '')
        ->call('submit')
        ->assertHasErrors(['form.timeFrom'])
        ->call('saveDraft')
        ->assertHasErrors(['form.timeFrom']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.timeFrom', 'invalid time')
        ->call('submit')
        ->assertHasErrors(['form.timeFrom'])
        ->call('saveDraft')
        ->assertHasErrors(['form.timeFrom']);
});

test('a timeUntil must be valid', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('submit')
        ->assertHasErrors(['form.timeUntil'])
        ->call('saveDraft')
        ->assertHasErrors(['form.timeUntil']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.timeUntil', 'invalid time')
        ->call('submit')
        ->assertHasErrors(['form.timeUntil'])
        ->call('saveDraft')
        ->assertHasErrors(['form.timeUntil']);
});

test('a reason must be valid', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('submit')
        ->assertHasErrors('form.reason')
        ->call('saveDraft')
        ->assertHasErrors('form.reason');

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.reason', min(array_keys(OvertimeReason::toArray())) - 1)
        ->call('submit')
        ->assertHasErrors(['form.reason'])
        ->call('saveDraft')
        ->assertHasErrors(['form.reason']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form.reason', max(array_keys(OvertimeReason::toArray())) + 1)
        ->call('submit')
        ->assertHasErrors(['form.reason'])
        ->call('saveDraft')
        ->assertHasErrors(['form.reason']);
});

test('a remark must be valid', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('submit')
        ->assertHasNoErrors('form.remarks')
        ->call('saveDraft')
        ->assertHasNoErrors('form.remarks')
        ->set('form.remarks', 'no')
        ->call('submit')
        ->assertHasErrors('form.remarks')
        ->call('saveDraft')
        ->assertHasErrors('form.remarks');
});

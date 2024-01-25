<?php

use App\Livewire\Overtime\Index;
use App\Models\OvertimeConfirmation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can see the page', function() {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/overtime')
        ->assertSeeLivewire(Index::class);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee($user->name)
        ->assertSet('year', now()->year)
        ->assertSet('month', now()->month);
});

test('the current date can be changed', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSet('year', now()->year)
        ->call('decreaseYear')
        ->assertSet('year', now()->subYear()->year)
        ->call('increaseYear')
        ->assertSet('year', now()->year)
        ->call('decreaseMonth')
        ->assertSet('month', now()->subMonth()->month)
        ->call('increaseMonth')
        ->assertSet('month', now()->month);

    Livewire::actingAs($user)
        ->withQueryParams(['year' => 2015, 'month' => 5])
        ->test(Index::class)
        ->assertSet('year', 2015)
        ->assertSet('month', 5);
});

test('the date cannot be set after current year and month', function() {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSet('year', now()->year)
        ->call('increaseYear')
        ->assertSet('year', now()->year)
        ->assertSet('month', now()->month)
        ->call('increaseMonth')
        ->assertSet('year', now()->year)
        ->assertSet('month', now()->month);

    Livewire::actingAs($user)
        ->withQueryParams(['year' => now()->addYear()->year, 'month' => now()->addYear()->addMonth()->month])
        ->test(Index::class)
        ->assertSet('year', now()->year)
        ->assertSet('month', now()->month);
});

test('an overtime confirmation can be submitted', function() {
    $user = User::factory()->create();

    $this->assertDatabaseMissing('overtime_confirmations', [
        'user_id' => $user->id,
        'month' => now()->month,
        'year' => now()->year,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSet('uuid', '')
        ->assertSee('最終確認')
        ->assertDontSee('当月分の申請は以上になります。')
        ->call('submit');

    $this->assertDatabaseHas('overtime_confirmations', [
        'user_id' => $user->id,
        'month' => now()->month,
        'year' => now()->year,
    ]);

    $overtimeConfirmation = OvertimeConfirmation::where([
        'user_id' => $user->id,
        'month' => now()->month,
        'year' => now()->year,
    ])->first();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('当月分の申請は以上になります。')
        ->assertSet('uuid', $overtimeConfirmation->uuid);
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('A guest cannot view the overtime creation page', function() {
    $response = $this->get('/overtime/create');

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_FOUND);
    $response->assertRedirect('/login');
});

test('A user can see their overtime creation page', function() {
    $user = User::factory()->create();
    $response = $this
        ->actingAs($user)
        ->get('/overtime/create');

    $response->assertStatus(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    $response->assertSee($user->name);
});

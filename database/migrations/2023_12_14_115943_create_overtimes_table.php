<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtimes', function(Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('overtime_confirmation_id')->references('id')->on('overtime_confirmations');
            $table->date('date');
            $table->time('time_from');
            $table->time('time_until');
            $table->text('reason');
            $table->text('remarks')->nullable();
            $table->foreignId('created_user_id')->references('id')->on('users');
            $table->foreignId('applicant_user_id')->nullable()->references('id')->on('users');
            $table->dateTime('applied_at')->nullable();
            $table->foreignId('approval_user_id')->nullable()->references('id')->on('users');
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('overtime_confirmation_id')->references('id')->on('overtime_confirmations');
            $table->date('date');
            $table->unsignedTinyInteger('from_hours');
            $table->unsignedTinyInteger('from_minutes');
            $table->unsignedTinyInteger('to_hours');
            $table->unsignedTinyInteger('to_minutes');
            $table->unsignedTinyInteger('reason');
            $table->string('remarks')->nullable();
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

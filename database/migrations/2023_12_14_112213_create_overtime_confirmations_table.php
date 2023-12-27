<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('overtime_confirmations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->text('remarks')->nullable();
            $table->text('transfer_remarks')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['uuid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_confirmations');
    }
};

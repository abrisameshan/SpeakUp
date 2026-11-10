<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspiration_upvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('aspiration_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Cegah 1 user melakukan upvote lebih dari 1 kali pada aspirasi yang sama
            $table->unique(['user_id', 'aspiration_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aspiration_upvotes');
    }
};

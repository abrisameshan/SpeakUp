<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {$table->enum('role', ['siswa', 'guru', 'admin'])->default('siswa')->after('email');
        });

        Schema::table('aspirations', function (Blueprint $table) {$table->text('feedback')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {$table->dropColumn('role');
        });

        Schema::table('aspirations', function (Blueprint $table) {$table->dropColumn('feedback');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('status')->default('off')->after('note'); // off, working, on_break, ended
            $table->boolean('on_break')->default(false)->after('status');
            $table->time('break_started_at')->nullable()->after('on_break');
            $table->time('break_ended_at')->nullable()->after('break_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['status', 'on_break', 'break_started_at', 'break_ended_at']);
        });
    }
};

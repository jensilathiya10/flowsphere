<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_workspace_id')->nullable()->after('id')
                ->constrained('workspaces')->nullOnDelete();
            $table->string('job_title')->nullable()->after('name');
            $table->string('avatar_path')->nullable()->after('job_title');
            $table->timestamp('last_active_at')->nullable()->after('avatar_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_workspace_id');
            $table->dropColumn(['job_title', 'avatar_path', 'last_active_at']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->default('website')->after('category'); // website | app | both
            $table->string('app_store_url')->nullable()->after('repo_url');
            $table->string('play_store_url')->nullable()->after('app_store_url');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['type', 'app_store_url', 'play_store_url']);
        });
    }
};

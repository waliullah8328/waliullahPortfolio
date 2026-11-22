<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Projects keep the category name in projects.category; start the list with the names already in use.
        $names = DB::table('projects')->whereNotNull('category')->where('category', '!=', '')
            ->distinct()->orderBy('category')->pluck('category');

        $now = now();
        foreach ($names as $name) {
            DB::table('project_categories')->insert(['name' => $name, 'sort_order' => 0, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_categories');
    }
};

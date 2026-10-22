<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('task_task_category')) {
            Schema::create('task_task_category', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('task_id');
                $table->unsignedInteger('task_category_id');
                $table->timestamps();

                $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
                $table->foreign('task_category_id')->references('id')->on('task_category')->onDelete('cascade');

                $table->unique(['task_id', 'task_category_id'], 'task_task_category_unique');
            });
        }

        // Migrate existing task_category_id from tasks table to pivot table
        if (Schema::hasColumn('tasks', 'task_category_id')) {
            $existingTasks = DB::table('tasks')
                ->whereNotNull('task_category_id')
                ->select('id as task_id', 'task_category_id')
                ->get();

            $now = now();
            $rows = [];
            foreach ($existingTasks as $item) {
                // Check if category exists
                $categoryExists = DB::table('task_category')->where('id', $item->task_category_id)->exists();
                if ($categoryExists) {
                    $rows[] = [
                        'task_id'          => $item->task_id,
                        'task_category_id' => $item->task_category_id,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];
                }
            }

            if (!empty($rows)) {
                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('task_task_category')->insertOrIgnore($chunk);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_task_category');
    }
};

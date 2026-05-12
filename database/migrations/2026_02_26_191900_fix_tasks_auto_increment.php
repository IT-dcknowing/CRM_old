<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixTasksAutoIncrement extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tasks')) {
            // On ajoute PRIMARY KEY et AUTO_INCREMENT sur la colonne id
            // car MySQL exige qu'une colonne AUTO_INCREMENT soit une clé
            $hasPk = !empty(DB::select("SHOW INDEX FROM tasks WHERE Key_name = 'PRIMARY'"));
            $pkClause = $hasPk ? "" : " PRIMARY KEY";
            DB::statement("ALTER TABLE tasks MODIFY id INT UNSIGNED AUTO_INCREMENT$pkClause");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixEmployeeActivityAutoIncrement extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('employee_activity')) {
            // Supprimer le doublon potentiel ID 0 pour permettre l'auto-incrément
            DB::table('employee_activity')->where('id', 0)->delete();

            // Utiliser une instruction SQL directe pour corriger l'auto-incrément
            // On ajoute PRIMARY KEY seulement si elle n'existe pas déjà
            $hasPk = !empty(DB::select("SHOW INDEX FROM employee_activity WHERE Key_name = 'PRIMARY'"));
            $pkClause = $hasPk ? "" : " PRIMARY KEY";
            DB::statement("ALTER TABLE employee_activity MODIFY id BIGINT UNSIGNED AUTO_INCREMENT$pkClause");
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
;

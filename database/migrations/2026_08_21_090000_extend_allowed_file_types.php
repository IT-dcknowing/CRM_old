<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Complete la liste des types de fichiers autorises au televersement.
 *
 * La liste d'origine ne couvrait ni PowerPoint, ni CSV, ni les formats
 * OpenDocument, ni les extensions simples (.doc, .xlsx, .pptx...).
 * Les valeurs deja presentes sont conservees : on ne fait qu'ajouter
 * celles qui manquent, sans ecraser un reglage choisi par l'utilisateur.
 */
return new class extends Migration
{
    /** Types ajoutes s'ils sont absents. */
    private const ADDITIONS = [
        // PowerPoint
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        // OpenDocument (LibreOffice)
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
        // Texte et donnees
        'text/csv',
        'application/csv',
        'application/rtf',
        // Archives
        'application/x-rar-compressed',
        'application/x-7z-compressed',
        // Extensions simples : Dropzone les accepte et elles couvrent les cas
        // ou le navigateur n'envoie pas le bon type MIME.
        '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
        '.pdf', '.csv', '.txt', '.odt', '.ods', '.odp',
        '.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg',
        '.zip', '.rar', '.7z',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('global_settings')
            || !Schema::hasColumn('global_settings', 'allowed_file_types')) {
            return;
        }

        foreach (DB::table('global_settings')->select('id', 'allowed_file_types')->get() as $row) {
            $current = array_filter(array_map('trim', explode(',', (string) $row->allowed_file_types)));

            $merged = $current;

            foreach (self::ADDITIONS as $type) {
                if (!in_array($type, $merged, true)) {
                    $merged[] = $type;
                }
            }

            if ($merged !== $current) {
                DB::table('global_settings')
                    ->where('id', $row->id)
                    ->update(['allowed_file_types' => implode(',', $merged)]);
            }
        }
    }

    /**
     * Retire uniquement les entrees ajoutees par cette migration, en laissant
     * intactes celles qui existaient auparavant.
     */
    public function down(): void
    {
        if (!Schema::hasTable('global_settings')
            || !Schema::hasColumn('global_settings', 'allowed_file_types')) {
            return;
        }

        foreach (DB::table('global_settings')->select('id', 'allowed_file_types')->get() as $row) {
            $kept = array_values(array_filter(
                array_map('trim', explode(',', (string) $row->allowed_file_types)),
                fn ($type) => $type !== '' && !in_array($type, self::ADDITIONS, true)
            ));

            DB::table('global_settings')
                ->where('id', $row->id)
                ->update(['allowed_file_types' => implode(',', $kept)]);
        }
    }
};

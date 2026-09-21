<?php

namespace Tests\Seeders;

use App\Models\GlobalSetting;
use Database\Seeders\CoreDatabaseSeeder;
use Database\Seeders\CountriesTableSeeder;
use Database\Seeders\ModulePermissionSeeder;
use Database\Seeders\SmtpSettingsSeeder;
use Illuminate\Database\Seeder;

/**
 * Socle global minimal pour les tests (une seule execution par run, via
 * RefreshDatabase::$seeder). Remplace Database\Seeders\DatabaseSeeder qui,
 * pour amorcer une societe de demonstration, cree en cascade des centaines
 * de lignes (utilisateurs Faker, leads, tickets, estimations...) et
 * declenche de vrais evenements applicatifs (notification Slack a la
 * creation de chaque utilisateur, entre autres) : inutilisable tel quel
 * dans une suite de tests automatisee.
 */
class TestGlobalCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(SmtpSettingsSeeder::class);
        $this->call(CoreDatabaseSeeder::class);

        $globalSetting = new GlobalSetting();
        $globalSetting->global_app_name = 'Test Suite';
        $globalSetting->locale = 'en';
        $globalSetting->google_recaptcha_status = 'deactive';
        $globalSetting->google_recaptcha_v2_status = 'deactive';
        $globalSetting->google_recaptcha_v3_status = 'deactive';
        $globalSetting->app_debug = false;
        $globalSetting->rtl = false;
        $globalSetting->hide_cron_message = 0;
        $globalSetting->system_update = 1;
        $globalSetting->show_review_modal = 1;
        $globalSetting->auth_theme = 'light';
        $globalSetting->session_driver = 'array';
        $globalSetting->allowed_file_size = 10;
        $globalSetting->moment_format = 'DD-MM-YYYY';
        $globalSetting->allowed_file_types = 'image/*,application/pdf';
        $globalSetting->show_update_popup = 0;
        $globalSetting->hash = md5(microtime());
        $globalSetting->save();

        $this->call(ModulePermissionSeeder::class);
    }
}

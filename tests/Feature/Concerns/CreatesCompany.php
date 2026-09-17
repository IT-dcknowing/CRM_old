<?php

namespace Tests\Feature\Concerns;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Construit le socle minimal (societe + roles + utilisateur admin) requis
 * par les controleurs de l'application, qui supposent toujours une societe
 * et un utilisateur authentifie deja en place (helpers company()/user()).
 *
 * Le catalogue global (permission_types, permissions...) est seme une seule
 * fois par tout le processus de test via "protected $seed = true" (mecanisme
 * RefreshDatabase natif, execute avant le debut des transactions par test).
 * Le resemer a chaque test casse les identifiants "en dur" utilises par
 * RolePermissionController, car MySQL/InnoDB n'annule pas l'auto-increment
 * lors d'un rollback de transaction.
 */
trait CreatesCompany
{
    protected $seed = true;

    protected $seeder = \Tests\Seeders\TestGlobalCatalogSeeder::class;

    protected function createCompanyWithAdmin(array $companyOverrides = []): array
    {
        $company = Company::create(array_merge([
            'company_name' => 'Cabinet Test',
            'company_email' => 'cabinet@test.local',
            'company_phone' => '0100000000',
            'address' => '1 rue de test',
            'before_days' => 1,
            'after_days' => 1,
            'allow_client_signup' => 0,
            'admin_client_signup_approval' => 0,
        ], $companyOverrides));

        $adminRole = Role::where('company_id', $company->id)->where('name', 'admin')->firstOrFail();

        $admin = $this->createUser($company->id, $adminRole->id, 'admin@test.local');

        return [$company, $admin];
    }

    protected function createEmployee(Company $company): User
    {
        $employeeRole = Role::where('company_id', $company->id)->where('name', 'employee')->firstOrFail();

        return $this->createUser($company->id, $employeeRole->id, 'employee' . uniqid() . '@test.local');
    }

    private function createUser(int $companyId, int $roleId, string $email): User
    {
        $user = new User();
        $user->name = 'Utilisateur Test';
        $user->company_id = $companyId;
        $user->email = $email;
        $user->password = Hash::make('password');
        $user->gender = 'male';
        $user->email_notifications = 1;
        $user->status = 'active';
        $user->save();

        $user->roles()->attach($roleId);

        return $user->fresh();
    }
}

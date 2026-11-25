<?php

namespace Tests\Feature;

use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractRenewController;
use App\Http\Requests\Admin\Contract\RenewRequest;
use App\Http\Requests\Admin\Contract\StoreRequest;
use App\Http\Requests\Admin\Contract\UpdateRequest;
use App\Models\ClientDetails;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractRenew;
use App\Models\ContractType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Concerns\CreatesCompany;
use Tests\TestCase;

/**
 * Verifie que les dates saisies dans le formulaire contrat restent la source
 * de verite de l'aperçu et du document final, y compris apres modification
 * et renouvellement.
 */
class ContractDateFidelityTest extends TestCase
{
    use RefreshDatabase;
    use CreatesCompany {
        createCompanyWithAdmin as createCompanyWithAdminFromTrait;
    }

    private const START_DATE = '25-11-2026';
    private const END_DATE = '25-11-2027';

    public function test_creation_stores_form_dates_and_they_reach_preview_and_pdf(): void
    {
        [$company, $admin] = $this->createCompanyWithAdmin([
            'timezone' => 'Africa/Abidjan',
            'date_format' => 'd-m-Y',
        ]);
        $this->actingAs($admin);

        [$client, $clientDetails] = $this->createClient($company);
        $type = $this->createContractType($company, 'Nouvelle adhésion');

        $request = StoreRequest::create('/contracts', 'POST', [
            'contract_number' => 91001,
            'client_id' => $client->id,
            'subject' => 'Nouvelle adhésion',
            'amount' => 0,
            'contract_type' => $type->id,
            'start_date' => self::START_DATE,
            'end_date' => self::END_DATE,
            'chosen_channels' => [],
        ]);

        (new ContractController())->store($request);

        // Rechargement depuis la base : ce que l'utilisateur voit ensuite.
        $contract = Contract::firstOrFail()->fresh();
        $this->assertSame('2026-11-25', $contract->start_date->format('Y-m-d'));
        $this->assertSame('2027-11-25', $contract->end_date->format('Y-m-d'));
        $this->assertSame('2026-11-25', $clientDetails->fresh()->skype);

        $this->assertStringContainsString(
            'Fait à Abidjan, le 25/11/2026',
            html_entity_decode($this->renderSummary($contract, $company))
        );
        $this->assertStringContainsString(
            'Fait à Abidjan, le 25/11/2026',
            $this->renderPdf($contract, $company)
        );
    }

    public function test_updating_start_date_and_end_date_persists_and_refreshes_documents(): void
    {
        [$company, $admin] = $this->createCompanyWithAdmin([
            'timezone' => 'Africa/Abidjan',
            'date_format' => 'd-m-Y',
        ]);
        $this->actingAs($admin);

        [$client, $clientDetails] = $this->createClient($company, '2026-01-31');
        $type = $this->createContractType($company, 'Nouvelle adhésion');
        $contract = $this->createContract($company, $client, $type, self::START_DATE, self::END_DATE);

        $request = UpdateRequest::create('/contracts/' . $contract->id, 'PUT', [
            'contract_number' => $contract->contract_number,
            'client_id' => $client->id,
            'subject' => 'Nouvelle adhésion',
            'amount' => 0,
            'contract_type' => $type->id,
            'start_date' => '03-12-2026',
            'end_date' => '03-12-2027',
            'chosen_channels' => [],
        ]);

        (new ContractController())->update($request, $contract->id);

        $contract = Contract::findOrFail($contract->id)->fresh();
        $this->assertSame('2026-12-03', $contract->start_date->format('Y-m-d'));
        $this->assertSame('2027-12-03', $contract->end_date->format('Y-m-d'));
        $this->assertSame('2026-12-03', $clientDetails->fresh()->skype);

        $this->assertStringContainsString(
            'Fait à Abidjan, le 03/12/2026',
            html_entity_decode($this->renderSummary($contract, $company))
        );
        $this->assertStringContainsString(
            'Fait à Abidjan, le 03/12/2026',
            $this->renderPdf($contract, $company)
        );
    }

    public function test_renewal_form_dates_are_saved_on_renewal_and_parent_contract(): void
    {
        [$company, $admin] = $this->createCompanyWithAdmin([
            'timezone' => 'Africa/Abidjan',
            'date_format' => 'd-m-Y',
        ]);
        $this->actingAs($admin);

        [$client] = $this->createClient($company, '2026-01-31');
        $type = $this->createContractType($company, 'Renouvellement d\'attestation d\'adhésion');
        $contract = $this->createContract($company, $client, $type, self::START_DATE, self::END_DATE);

        $request = RenewRequest::create('/contract-renew', 'POST', [
            'contract_id' => $contract->id,
            'amount' => 0,
            'start_date' => '10-01-2027',
            'end_date' => '10-01-2028',
        ]);

        $controller = new ContractRenewController();
        $this->setControllerUser($controller, $admin);
        $controller->store($request);

        $contract = Contract::findOrFail($contract->id)->fresh();
        $renewal = ContractRenew::firstOrFail();

        $this->assertSame('2027-01-10', $renewal->start_date->format('Y-m-d'));
        $this->assertSame('2027-01-10', $contract->start_date->format('Y-m-d'));
        $this->assertSame('2028-01-10', $contract->end_date->format('Y-m-d'));
        $this->assertStringContainsString(
            'Fait à Abidjan, le 10/01/2027',
            $this->renderPdf($contract, $company)
        );
    }

    public function test_editing_a_renewal_propagates_new_dates_to_the_contract(): void
    {
        [$company, $admin] = $this->createCompanyWithAdmin([
            'timezone' => 'Africa/Abidjan',
            'date_format' => 'd-m-Y',
        ]);
        $this->actingAs($admin);

        [$client] = $this->createClient($company, '2026-01-31');
        $type = $this->createContractType($company, 'Renouvellement d\'attestation d\'adhésion');
        $contract = $this->createContract($company, $client, $type, self::START_DATE, self::END_DATE);

        $renewal = new ContractRenew();
        $renewal->contract_id = $contract->id;
        $renewal->renewed_by = $admin->id;
        $renewal->amount = 0;
        $renewal->start_date = '2026-11-25';
        $renewal->end_date = '2027-11-25';
        $renewal->save();

        $request = RenewRequest::create('/contract-renew/' . $renewal->id, 'PUT', [
            'amount' => 0,
            'start_date' => '15-02-2027',
            'end_date' => '15-02-2028',
        ]);

        $controller = new ContractRenewController();
        $this->setControllerUser($controller, $admin);
        $controller->update($request, $renewal->id);

        $contract = Contract::findOrFail($contract->id)->fresh();
        $this->assertSame('2027-02-15', $contract->start_date->format('Y-m-d'));
        $this->assertSame('2028-02-15', $contract->end_date->format('Y-m-d'));
        $this->assertStringContainsString(
            'Fait à Abidjan, le 15/02/2027',
            html_entity_decode($this->renderSummary($contract, $company))
        );
    }

    public function test_renew_button_keeps_recorded_dates_instead_of_using_today(): void
    {
        [$company, $admin] = $this->createCompanyWithAdmin([
            'timezone' => 'Africa/Abidjan',
            'date_format' => 'd-m-Y',
        ]);
        $this->actingAs($admin);

        [$client, $clientDetails] = $this->createClient($company);
        $type = $this->createContractType($company, 'Nouvelle adhésion');
        ContractType::firstOrCreate(
            ['id' => 4],
            ['company_id' => $company->id, 'name' => 'Renouvellement d\'attestation d\'adhésion']
        );
        $contract = $this->createContract($company, $client, $type, self::START_DATE, self::END_DATE);

        (new ContractController())->updateRenew($contract->id);

        $contract = Contract::findOrFail($contract->id)->fresh();
        $this->assertSame('2026-11-25', $contract->start_date->format('Y-m-d'));
        $this->assertSame('2027-11-25', $contract->end_date->format('Y-m-d'));
        $this->assertSame('2026-11-25', $clientDetails->fresh()->skype);
        $this->assertStringContainsString(
            'Fait à Abidjan, le 25/11/2026',
            $this->renderPdf($contract, $company)
        );
    }

    private function createClient(Company $company, ?string $skype = null): array
    {
        $client = new User();
        $client->name = 'Client Contrat';
        $client->company_id = $company->id;
        $client->email = 'client-contrat-' . uniqid() . '@test.local';
        $client->password = Hash::make('password');
        $client->gender = 'male';
        $client->email_notifications = 0;
        $client->status = 'active';
        $client->save();

        $role = Role::where('company_id', $company->id)->where('name', 'client')->firstOrFail();
        $client->roles()->attach($role->id);

        $details = ClientDetails::create([
            'company_id' => $company->id,
            'user_id' => $client->id,
            'company_name' => 'Société Client Test',
            'numadh' => 'ADH-123',
            'skype' => $skype,
        ]);

        return [$client->fresh(), $details];
    }

    private function createContractType(Company $company, string $name): ContractType
    {
        return ContractType::create([
            'company_id' => $company->id,
            'name' => $name,
        ]);
    }

    private function createContract(
        Company $company,
        User $client,
        ContractType $type,
        string $startDate,
        string $endDate
    ): Contract {
        $contract = new Contract();
        $contract->company_id = $company->id;
        $contract->client_id = $client->id;
        $contract->contract_number = 'C-' . uniqid();
        $contract->subject = $type->name;
        $contract->contract_name = $type->name;
        $contract->amount = 0;
        $contract->original_amount = 0;
        $contract->contract_type_id = $type->id;
        $contract->start_date = $startDate;
        $contract->original_start_date = $startDate;
        $contract->end_date = $endDate;
        $contract->original_end_date = $endDate;
        $contract->save();

        return $contract->fresh();
    }

    private function renderSummary(Contract $contract, Company $company): string
    {
        $contract->load('client.clientDetails', 'contractType');

        return view('contracts.ajax.summary', [
            'contract' => $contract,
            'company' => $company,
            'fields' => collect(),
            'editContractPermission' => 'all',
            'deleteContractPermission' => 'all',
            'addContractPermission' => 'all',
        ])->render();
    }

    private function renderPdf(Contract $contract, Company $company): string
    {
        $contract->load('client.clientDetails', 'contractType');

        return view('contracts.contract-pdf', [
            'contract' => $contract,
            'company' => $company,
            'fields' => collect(),
            'invoiceSetting' => (object) [
                'is_chinese_lang' => false,
                'locale' => 'en',
            ],
        ])->render();
    }

    private function setControllerUser(ContractRenewController $controller, User $admin): void
    {
        $property = new \ReflectionProperty($controller, 'user');
        $property->setAccessible(true);
        $property->setValue($controller, $admin);
    }
}

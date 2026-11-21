<?php

namespace App\Http\Controllers;

use App\DataTables\ContractsDataTable;
use App\Events\ContractSignedEvent;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Admin\Contract\StoreRequest;
use App\Http\Requests\Admin\Contract\UpdateRequest;
use App\Http\Requests\ClientContracts\SignRequest;
use App\Models\BaseModel;
use App\Models\Contract;
use App\Models\ContractRenew;
use App\Models\ContractSign;
use App\Models\ContractTemplate;
use App\Models\ContractType;
use App\Models\ClientDetails;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ContractController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.contracts';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('contracts', $this->user->modules));

            return $next($request);
        });
    }

    public function index(ContractsDataTable $dataTable)
    {
        abort_403(user()->permission('view_contract') == 'none');

        if (!request()->ajax()) {
            $this->projects = Project::allProjects();

            if (in_array('client', user_roles())) {
                $this->clients = User::client();
            }
            else {
                $this->clients = User::allClients();
            }

            $this->contractTypes = ContractType::all();

            /* Compteurs restreints aux memes droits que la liste, et calcules
               avec les memes regles de statut que les badges du tableau. */
            $compteurs = $this->compteursContrats();
            $this->contractCounts = $compteurs['total'];
            $this->expiredCounts = $compteurs['expires'];
            $this->aboutToExpireCounts = $compteurs['bientot'];
        }

        return $dataTable->render('contracts.index', $this->data);
    }

    public function applyQuickAction(Request $request)
    {
        if ($request->action_type == 'delete') {
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        }

        return Reply::error(__('messages.selectAction'));
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_contract') !== 'all');

        Contract::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;

    }

    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $this->deletePermission = user()->permission('delete_contract');

        abort_403(!(
            $this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && user()->id == $contract->added_by)
            || ($this->deletePermission == 'owned' && user()->id == $contract->client_id)
            || ($this->deletePermission == 'both' && (user()->id == $contract->client_id || user()->id == $contract->added_by)
            )));

        Contract::destroy($id);

        /* Les compteurs sont rendus au chargement de la page : sans ces
           valeurs, le total restait fige apres une suppression et il
           fallait recharger l'ecran pour le voir baisser. */
        return Reply::successWithData(__('messages.deleteSuccess'), [
            'compteurs' => $this->compteursContrats(),
        ]);

    }

    /**
     * Compteurs de l'en-tete de la liste, restreints aux memes droits
     * que le tableau lui-meme.
     */
    private function compteursContrats(): array
    {
        $portee = function () {
            $q = Contract::query();
            $droit = user()->permission('view_contract');

            if ($droit == 'added') {
                $q->where('added_by', user()->id);
            }
            elseif ($droit == 'owned') {
                $q->where('client_id', user()->id);
            }
            elseif ($droit == 'both') {
                $q->where(fn ($s) => $s->where('added_by', user()->id)->orWhere('client_id', user()->id));
            }

            return $q;
        };

        $total = $portee()->count();
        $expires = $portee()->expires()->count();
        $bientot = $portee()->bientotExpires()->count();

        return [
            'total'   => $total,
            'actifs'  => max($total - $expires, 0),
            'bientot' => $bientot,
            'expires' => $expires,
        ];
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_contract');
        abort_403(!in_array('clients', user_modules()) || !in_array($this->addPermission, ['all', 'added']));

        $this->contractId = request('id');
        $this->contract = null;

        if ($this->contractId != '') {
            $this->contractTemplate = Contract::findOrFail($this->contractId);
        }

        $this->templates = ContractTemplate::all();
        if (in_array('apporteurs', user_roles())) {
            $this->clients = User::allClients(active:false);
        }
        else {
            $this->clients = User::allClients(null, overRidePermission:($this->addPermission == 'all' ? 'all' : null));
        }
        
        $this->contractTypes = ContractType::all();
        $this->currencies = Currency::all();
        $this->projects = Project::all();

        // Date de debut proposee : le jour de l'enregistrement, modifiable.
        $this->dateDebutParDefaut = Carbon::parse(Contract::aujourdhui())->format(company()->date_format);

        $this->lastContract = Contract::lastContractNumber() + 1;
        $this->invoiceSetting = invoice_setting();
        $this->zero = '';

        if (strlen($this->lastContract) < $this->invoiceSetting->contract_digit) {
            $condition = $this->invoiceSetting->contract_digit - strlen($this->lastContract);

            for ($i = 0; $i < $condition; $i++) {
                $this->zero = '0' . $this->zero;
            }
        }


        if (is_null($this->contractId)) {
            $this->contractTemplate = request('template') ? ContractTemplate::findOrFail(request('template')) : null;
        }

        $contract = new Contract();
        $getCustomFieldGroupsWithFields = $contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $this->pageTitle = __('app.menu.addContract');

        $this->view = 'contracts.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('contracts.create', $this->data);

    }

    public function store(StoreRequest $request)
    {
        /* Date d'adhesion du client et contrat sont enregistres ensemble :
           si le contrat echoue, la date d'adhesion n'est pas conservee. */
        $contract = DB::transaction(function () use ($request) {
            $this->definirDateAdhesionClient($request);

            return $this->storeUpdate($request, new Contract());
        });

        $this->notifierContrat($request, $contract);

        return Reply::redirect(route('contracts.index'), __('messages.recordSaved'));
    }

    /**
     * Date d'adhesion du client (stockee dans client_details.skype), fixee
     * une seule fois, au premier contrat. Regle metier inchangee : pour une
     * "Nouvelle adhesion" signee apres janvier, l'adhesion est datee du
     * 31 janvier de l'annee en cours.
     */
    private function definirDateAdhesionClient($request): void
    {
        $clientDetails = ClientDetails::where('user_id', $request->client_id)->first();

        if (!$clientDetails || !empty($clientDetails->skype)) {
            return;
        }

        $aujourdhui = Carbon::parse(Contract::aujourdhui());
        $adhesionDate = $aujourdhui->copy();

        $contractType = ContractType::find($request->contract_type_id ?? $request->contract_type);

        if ($contractType?->estNouvelleAdhesion() && $aujourdhui->month !== 1) {
            $adhesionDate = Carbon::create($aujourdhui->year, 1, 31);
        }

        $clientDetails->skype = $adhesionDate->format('Y-m-d');
        $clientDetails->save();
    }


    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_contract');
        $this->contract = Contract::with('signature', 'renewHistory', 'renewHistory.renewedBy')
            ->findOrFail($id)
            ->withCustomFields();

        $this->projects = Project::all();


        abort_403(!in_array('clients', user_modules()) || (!(
            $this->editPermission == 'all'
            || ($this->editPermission == 'added' && user()->id == $this->contract->added_by)
            || ($this->editPermission == 'owned' && user()->id == $this->contract->client_id)
            || ($this->editPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by)
            ))));

        $this->clients = User::allClients(null, overRidePermission:($this->editPermission == 'all' ? 'all' : null));
        $this->contractTypes = ContractType::all();
        $this->currencies = Currency::all();
        $this->pageTitle = $this->contract->contract_number;

        $contract = new Contract();

        $getCustomFieldGroupsWithFields = $contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $this->view = 'contracts.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('contracts.create', $this->data);

    }

    public function update(UpdateRequest $request, $id)
    {
        $contract = Contract::findOrFail($id);
        $this->storeUpdate($request, $contract);
        $this->notifierContrat($request, $contract);

        return Reply::redirect(route('contracts.index'), __('messages.updateSuccess'));
    }

    private function storeUpdate($request, $contract)
    {
        $contract->client_id = $request->client_id;
        $contract->project_id = $request->project_id;
        $contract->subject = $request->subject;
        $contract->amount = $request->amount;
        $contract->currency_id = $request->currency_id;
        $contract->original_amount = $request->amount;
        $contract->contract_name = $request->contract_name;
        $contract->alternate_address = $request->alternate_address;
        $contract->contract_note = $request->note;
        $contract->cell = $request->cell;
        $contract->office = $request->office;
        $contract->city = $request->city;
        $contract->state = $request->state;
        $contract->country = $request->country;
        $contract->postal_code = $request->postal_code;
        
        $contract->contract_type_id = $request->contract_type_id ?? $request->contract_type ?? $contract->contract_type_id;
        $contract->contract_number = $request->contract_number;

        /* Dates : toujours celles saisies dans le formulaire, quel que soit
           le type de contrat (le formulaire propose des valeurs par defaut,
           modifiables). La date de fin est deja ramenee a null par la
           validation quand "Sans date d'echeance" est coche. */
        $contract->start_date = companyToYmd($request->start_date);
        $contract->end_date = companyToYmd($request->end_date);

        // Les dates d'origine sont figees a la creation : elles servent a
        // restaurer le contrat quand tous ses renouvellements sont supprimes.
        if (!$contract->exists) {
            $contract->original_start_date = $contract->start_date;
            $contract->original_end_date = $contract->end_date;
        }

        $contract->description = trim_editor($request->description);
        $contract->contract_detail = trim_editor($request->description);
        $contract->save();

        // To add custom fields data
        if ($request->custom_fields_data) {
            $contract->updateCustomFieldData($request->custom_fields_data);
        }

        return $contract;
    }

    private function notifierContrat($request, Contract $contract): void
    {
        try {
            $dispatcher = new \App\Services\NotificationDispatcher();
            $channels = $request->chosen_channels ?? ['email', 'whatsapp'];
            $dispatcher->dispatchContractInit($contract, $channels);
        } catch (\Throwable $e) {
            \Log::error('Error dispatching contract notifications: ' . $e->getMessage());
        }
    }

    public function updateRenew($id)
    {
        $contract = Contract::findOrFail($id);

        $editPermission = user()->permission('edit_contract');
        abort_403(!(
            $editPermission == 'all'
            || ($editPermission == 'added' && user()->id == $contract->added_by)
            || ($editPermission == 'owned' && user()->id == $contract->client_id)
            || ($editPermission == 'both' && (user()->id == $contract->client_id || user()->id == $contract->added_by))
        ));

        try {
            DB::transaction(function () use ($contract) {
                $clientDetails = $contract->client?->clientDetails;

                // Type "Renouvellement d'attestation d'adhesion"
                $contract->subject = 'Renouvellement d\'attestation d\'adhésion';
                $contract->amount = 0;
                $contract->contract_name = 'Renouvellement d\'attestation d\'adhésion';
                $contract->contract_type_id = 4;
                $contract->sign_date = now();

                // Nouvelle periode d'un an a partir d'aujourd'hui (dates
                // modifiables ensuite depuis le formulaire du contrat).
                $debut = Carbon::parse(Contract::aujourdhui());
                $contract->start_date = $debut->toDateString();
                $contract->end_date = $debut->copy()->addYear()->toDateString();
                $contract->save();

                // Trace dans l'historique des renouvellements, comme un
                // renouvellement saisi depuis l'onglet dedie.
                $renouvellement = new ContractRenew();
                $renouvellement->contract_id = $contract->id;
                $renouvellement->renewed_by = user()->id;
                $renouvellement->amount = 0;
                $renouvellement->start_date = $contract->start_date->toDateString();
                $renouvellement->end_date = $contract->end_date->toDateString();
                $renouvellement->save();

                // La date d'adhesion existante est conservee
                if ($clientDetails && empty($clientDetails->skype)) {
                    $clientDetails->skype = $debut->format('Y-m-d');
                    $clientDetails->save();
                }
            });

            return Reply::redirect(route('clients.index'), __('messages.updateSuccess'));

        } catch (\Exception $e) {
            // En cas d'erreur, renvoyer un message d'erreur
            return Reply::error('Erreur lors du renouvellement du contrat : ' . $e->getMessage());
        }
    }


    /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|mixed|void
     */
    public function show($id)
    {
        $viewPermission = user()->permission('view_contract');
        $this->addContractPermission = user()->permission('add_contract');
        $this->editContractPermission = user()->permission('edit_contract');
        $this->deleteContractPermission = user()->permission('delete_contract');
        $this->viewDiscussionPermission = $viewDiscussionPermission = user()->permission('view_contract_discussion');
        $this->viewContractFilesPermission = $viewContractFilesPermission = user()->permission('view_contract_files');

        $this->contract = Contract::with(['signature', 'client', 'client.clientDetails', 'files' => function ($q) use ($viewContractFilesPermission) {
            if ($viewContractFilesPermission == 'added') {
                $q->where('added_by', user()->id);
            }
        }, 'renewHistory', 'renewHistory.renewedBy',
            'discussion' => function ($q) use ($viewDiscussionPermission) {
                if ($viewDiscussionPermission == 'added') {
                    $q->where('contract_discussions.added_by', user()->id);
                }
            }, 'discussion.user'])->findOrFail($id)->withCustomFields();
        abort_403(!(
            $viewPermission == 'all'
            || ($viewPermission == 'added' && user()->id == $this->contract->added_by)
            || ($viewPermission == 'owned' && user()->id == $this->contract->client_id)
            || ($viewPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by))
        ));

        $this->users = User::where('id', $this->contract->client_id);

        $contract = new contract();

        $this->contracts = Contract::findOrFail($id);

        $getCustomFieldGroupsWithFields = $contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $this->pageTitle = $this->contract->contract_number;

        $tab = request('tab');

        $this->view = match ($tab) {
            'discussion' => 'contracts.ajax.discussion',
            'files' => 'contracts.ajax.files',
            'renew' => 'contracts.ajax.renew',
            default => 'contracts.ajax.summary',
        };


        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('contracts.show', $this->data);

    }
    
    public function showrenew($id)
    {
        $viewPermission = user()->permission('view_contract');
        $this->addContractPermission = user()->permission('add_contract');
        $this->editContractPermission = user()->permission('edit_contract');
        $this->deleteContractPermission = user()->permission('delete_contract');
        $this->viewDiscussionPermission = $viewDiscussionPermission = user()->permission('view_contract_discussion');
        $this->viewContractFilesPermission = $viewContractFilesPermission = user()->permission('view_contract_files');

        $this->contract = Contract::with(['signature', 'client', 'client.clientDetails', 'files' => function ($q) use ($viewContractFilesPermission) {
            if ($viewContractFilesPermission == 'added') {
                $q->where('added_by', user()->id);
            }
        }, 'renewHistory', 'renewHistory.renewedBy',
            'discussion' => function ($q) use ($viewDiscussionPermission) {
                if ($viewDiscussionPermission == 'added') {
                    $q->where('contract_discussions.added_by', user()->id);
                }
            }, 'discussion.user'])->findOrFail($id)->withCustomFields();
        abort_403(!(
            $viewPermission == 'all'
            || ($viewPermission == 'added' && user()->id == $this->contract->added_by)
            || ($viewPermission == 'owned' && user()->id == $this->contract->client_id)
            || ($viewPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by))
        ));

        $contract = new contract();

        $this->contracts = Contract::findOrFail($id);

        $getCustomFieldGroupsWithFields = $contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $this->pageTitle = $this->contract->contract_number;

        $tab = request('tab');

        $this->view = match ($tab) {
            'discussion' => 'contracts.ajax.discussion',
            'files' => 'contracts.ajax.files',
            'renew' => 'contracts.ajax.renew',
            default => 'contracts.ajax.summary',
        };


        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('contracts.show', $this->data);

    }

    // public function download($id)
    // {
    //     $this->contract = Contract::findOrFail($id);
    //     $viewPermission = user()->permission('view_contract');
    //     $this->contract = Contract::with('signature', 'client', 'client.clientDetails', 'files')->findOrFail($id)->withCustomFields();

    //     $getCustomFieldGroupsWithFields = $this->contract->getCustomFieldGroupsWithFields();

    //     if ($getCustomFieldGroupsWithFields) {
    //         $this->fields = $getCustomFieldGroupsWithFields->fields;
    //     }

    //     abort_403(!(
    //         $viewPermission == 'all'
    //         || ($viewPermission == 'added' && user()->id == $this->contract->added_by)
    //         || ($viewPermission == 'owned' && user()->id == $this->contract->client_id)
    //         || ($viewPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by))
    //     ));


    //     $pdf = app('dompdf.wrapper');

    //     $this->company = $this->settings = company();

    //     $this->invoiceSetting = invoice_setting();

    //     $pdf->setOptions([
    //         'defaultFont' => 'Arial', // Utiliser une police standard
    //         'isRemoteEnabled' => true,
    //     ]);

    //     //dd($this->data);

    //     App::setLocale($this->invoiceSetting->locale ?? 'en');
    //     Carbon::setLocale($this->invoiceSetting->locale ?? 'en');
    //     $pdf->loadView('contracts.contract-pdf', $this->data);

    //     $filename = 'contract-' . $this->contract->client->clientDetails->company_name;

    //     return $pdf->download($filename . '.pdf'); 

    // }
    
//     public function download($id)
// {
//     // Récupère le contrat et recharge ses dernières valeurs depuis la DB
//     $this->contract = Contract::findOrFail($id)->fresh();

//     // Permissions
//     $viewPermission = user()->permission('view_contract');
//     abort_403(!(
//         $viewPermission == 'all'
//         || ($viewPermission == 'added' && user()->id == $this->contract->added_by)
//         || ($viewPermission == 'owned' && user()->id == $this->contract->client_id)
//         || ($viewPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by))
//     ));

//     // Recharge les relations nécessaires et les champs personnalisés
//     $this->contract->load(['signature', 'client', 'client.clientDetails', 'files']);
//     $getCustomFieldGroupsWithFields = $this->contract->getCustomFieldGroupsWithFields();
//     if ($getCustomFieldGroupsWithFields) {
//         $this->fields = $getCustomFieldGroupsWithFields->fields;
//     }

//     // Informations de l'entreprise
//     $this->company = $this->settings = company();
//     $this->invoiceSetting = invoice_setting();

//     // Configuration PDF
//     $pdf = app('dompdf.wrapper');
//     $pdf->setOptions([
//         'defaultFont' => 'Arial',
//         'isRemoteEnabled' => true,
//     ]);

//     // Locale pour les dates dans le PDF
//     App::setLocale($this->invoiceSetting->locale ?? 'en');
//     Carbon::setLocale($this->invoiceSetting->locale ?? 'en');

//     // Génération du PDF
//     $pdf->loadView('contracts.contract-pdf', $this->data);

//     $filename = 'contract-' . $this->contract->client->clientDetails->company_name;

//     // Téléchargement
//     return $pdf->download($filename . '.pdf'); 
// }

public function download($id)
{
    $this->contract = Contract::findOrFail($id)->fresh();

    $viewPermission = user()->permission('view_contract');
    abort_403(!(
        $viewPermission == 'all'
        || ($viewPermission == 'added' && user()->id == $this->contract->added_by)
        || ($viewPermission == 'owned' && user()->id == $this->contract->client_id)
        || ($viewPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by))
    ));

    $this->contract->load(['signature', 'client', 'client.clientDetails', 'files']);
    $getCustomFieldGroupsWithFields = $this->contract->getCustomFieldGroupsWithFields();
    if ($getCustomFieldGroupsWithFields) {
        $this->fields = $getCustomFieldGroupsWithFields->fields;
    }

    $this->company = $this->settings = company();
    $this->invoiceSetting = invoice_setting();

    $pdf = app('dompdf.wrapper');
    $pdf->setOptions([
        'defaultFont' => 'Arial',
        'isRemoteEnabled' => true,
    ]);

    App::setLocale($this->invoiceSetting->locale ?? 'en');
    Carbon::setLocale($this->invoiceSetting->locale ?? 'en');

    $pdf->loadView('contracts.contract-pdf', $this->data);

    // Fallback si le client n’a pas de company_name
    $clientName = optional($this->contract->client->clientDetails)->company_name ?? 'client';
    $filename = 'contract-' . $clientName;

    return $pdf->download($filename . '.pdf'); 
}


    public function downloadView($id)
    {
        $this->contract = Contract::findOrFail($id)->withCustomFields();
        $pdf = app('dompdf.wrapper');

        $this->company = $this->settings = Company::findOrFail($this->contract->company_id);

        $this->invoiceSetting = invoice_setting();

        $getCustomFieldGroupsWithFields = $this->contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $pdf->setOption('enable_php', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');  
        $pdf->loadView('contracts.contract-pdf', $this->data);
        $clientName = optional($this->contract->client->clientDetails)->company_name ?? 'client';
        
        // $filename = 'contract-' . $this->contract->client->clientDetails->company_name;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    
    public function sign(SignRequest $request, $id)
    {
        $this->contract = Contract::with('signature')->findOrFail($id);

        if ($this->contract && $this->contract->signature) {
            return Reply::error(__('messages.alreadySigned'));
        }

        $sign = new ContractSign();
        $sign->full_name = $request->first_name . ' ' . $request->last_name;
        $sign->contract_id = $this->contract->id;
        $sign->email = $request->email;
        $sign->date = now();
        $sign->place = $request->place;
        $imageName = null;

        if ($request->signature_type == 'signature') {
            $image = $request->signature;  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = str_random(32) . '.' . 'jpg';
            Files::createDirectoryIfNotExist('contract/sign');

            File::put(public_path() . '/' . Files::UPLOAD_FOLDER . '/contract/sign/' . $imageName, base64_decode($image));
            Files::uploadLocalFile($imageName, 'contract/sign', $this->contract->company_id);
        }
        else {
            if ($request->hasFile('image')) {
                $imageName = Files::uploadLocalOrS3($request->image, 'contract/sign', 300);
            }
        }

        $sign->signature = $imageName;
        $sign->save();

        event(new ContractSignedEvent($this->contract, $sign));

        return Reply::redirect(route('contracts.show', $this->contract->id));
    }

    public function companySign(Request $request)
    {
        $contract = Contract::find($request->id);
        $imageName = null;

        if ($request->signature_type == 'signature') {
            $image = $request->signature;  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = str_random(32) . '.' . 'jpg';

            Files::createDirectoryIfNotExist('contract/sign');

            File::put(public_path() . '/' . Files::UPLOAD_FOLDER . '/contract/sign/' . $imageName, base64_decode($image));
            Files::uploadLocalFile($imageName, 'contract/sign', $contract->company_id);
        }
        else {
            if ($request->hasFile('image')) {
                $imageName = Files::uploadLocalOrS3($request->image, 'contract/sign', 300);
            }
        }

        $contract->company_sign = $imageName;
        $contract->sign_date = now();
        $contract->sign_by = user()->id;
        $contract->update();

        return Reply::successWithData(__('messages.signatureAdded'), ['status' => 'success']);


    }

    public function companiesSign(Request $request, $id)
    {
        $this->contract = Contract::find($id);

        return view('contracts.companysign.sign', $this->data);
    }

    public function projectDetail($id)
    {
        if ($id != 0) {
            $projects = Project::where('client_id', $id)->get();

        }
        else {
            $projects = Project::all();
        }

        $options = BaseModel::options($projects, null, 'project_name');

        return Reply::dataOnly(['status' => 'success', 'data' => $options]);
    }

    public function companySig($id)
    {
        $this->contract = Contract::find($id);

        return view('contracts.companysign.sign', $this->data);
    }

}

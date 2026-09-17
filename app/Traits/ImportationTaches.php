<?php

namespace App\Traits;

use App\Imports\TaskImport;
use App\Models\Task;
use App\Models\TaskboardColumn;
use App\Models\User;
use App\Notifications\TachesImportees;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Importation de taches depuis un fichier Excel.
 *
 * Deux temps, volontairement separes : analyser() lit et controle sans
 * rien ecrire, creer() enregistre en une seule transaction. C'est ce qui
 * permet de ne jamais laisser un fichier a moitie importe.
 *
 * Performance : toutes les correspondances — projets, collaborateurs,
 * categories, statuts — sont chargees une fois avant la boucle. Une
 * recherche par ligne produirait quatre requetes par tache, soit deux
 * cents requetes pour cinquante lignes.
 */
trait ImportationTaches
{

    /**
     * Lit le fichier et controle chaque ligne, sans rien enregistrer.
     *
     * @return object lignes analysees, erreurs, et compteurs
     */
    protected function analyserFichierTaches(string $chemin, ?int $projetDefaut = null, ?string $extension = null): object
    {
        /* Le type de lecteur est donne explicitement. Un fichier depose
           est lu depuis le dossier temporaire de PHP, et sous Linux ce
           fichier n'a pas d'extension (/tmp/phpA1b2C3) : la librairie ne
           pouvait pas deviner le format et levait NoTypeDetectedException
           en production, alors que tout passait sous Windows (.tmp). */
        $extension = strtolower($extension ?: pathinfo($chemin, PATHINFO_EXTENSION));

        $types = [
            'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
            'xls'  => \Maatwebsite\Excel\Excel::XLS,
            'ods'  => \Maatwebsite\Excel\Excel::ODS,
            'csv'  => \Maatwebsite\Excel\Excel::CSV,
        ];

        /* Le chemin est passe au lecteur : c'est de lui qu'il deduit le
           separateur et l'encodage d'un CSV. */
        $brut = \Maatwebsite\Excel\Facades\Excel::toArray(
            new TaskImport($chemin), $chemin, null, $types[$extension] ?? null
        );
        $feuille = $brut[0] ?? [];

        if (count($feuille) < 2) {
            return $this->resultatVide(__("Le fichier est vide ou ne contient que l'en-tête."));
        }

        $entetes = array_map(fn($v) => trim((string) $v), array_shift($feuille));
        $manquantes = $this->entetesManquantes($entetes);

        if ($manquantes) {
            return $this->resultatVide(
                __('Colonnes manquantes dans le fichier :') . ' ' . implode(', ', $manquantes)
            );
        }

        /* On ecarte les lignes entierement vides avant de compter. */
        $feuille = array_values(array_filter(
            $feuille,
            fn($l) => count(array_filter($l, fn($c) => trim((string) $c) !== '')) > 0
        ));

        if (count($feuille) > TaskImport::MAXIMUM_LIGNES) {
            return $this->resultatVide(sprintf(
                __('Le fichier contient %d lignes. Le maximum est de %d par import.'),
                count($feuille), TaskImport::MAXIMUM_LIGNES
            ));
        }

        $index = $this->indexColonnes($entetes);
        $refs = $this->referencesImport($projetDefaut);

        $lignes = [];
        $erreurs = [];
        $vus = [];

        foreach ($feuille as $n => $brute) {
            /* Numero affiche a l'utilisateur : la ligne 1 est l'en-tete. */
            $numero = $n + 2;

            $ligne = $this->analyserLigne($brute, $index, $refs, $numero, $vus);

            $lignes[] = $ligne;
            $erreurs = array_merge($erreurs, $ligne->erreurs);
        }

        $valides = collect($lignes)->filter(fn($l) => empty($l->erreurs))->count();

        return (object) [
            'ok'        => empty($erreurs),
            'message'   => null,
            'lignes'    => $lignes,
            'erreurs'   => $erreurs,
            'total'     => count($lignes),
            'valides'   => $valides,
            'enErreur'  => count($lignes) - $valides,
        ];
    }

    private function resultatVide(string $message): object
    {
        return (object) [
            'ok' => false, 'message' => $message, 'lignes' => [],
            'erreurs' => [$message], 'total' => 0, 'valides' => 0, 'enErreur' => 0,
        ];
    }

    private function entetesManquantes(array $entetes): array
    {
        $normalise = array_map([$this, 'normaliser'], $entetes);
        $manquantes = [];

        foreach (TaskImport::colonnes() as $c) {
            if ($this->positionColonne($c, $normalise) === null) {
                $manquantes[] = $c['nom'];
            }
        }

        return $manquantes;
    }

    private function indexColonnes(array $entetes): array
    {
        $normalise = array_map([$this, 'normaliser'], $entetes);
        $index = [];

        foreach (TaskImport::colonnes() as $c) {
            $index[$c['cle']] = $this->positionColonne($c, $normalise);
        }

        return $index;
    }

    /**
     * Position d'une colonne dans l'en-tete, ou null.
     *
     * L'intitule courant est cherche en premier, puis ceux qu'il a
     * remplaces : un fichier rempli avant le renommage doit continuer
     * de passer.
     *
     * @param array $entetesNormalises l'en-tete du fichier, deja normalise
     */
    private function positionColonne(array $colonne, array $entetesNormalises): ?int
    {
        foreach (TaskImport::intitulesAcceptes($colonne) as $intitule) {
            $p = array_search($this->normaliser($intitule), $entetesNormalises, true);

            if ($p !== false) {
                return $p;
            }
        }

        return null;
    }

    /**
     * Toutes les correspondances, chargees une seule fois.
     *
     * Les clefs sont normalisees — sans accent, sans casse, sans espace
     * superflu — pour qu'un fichier ecrit « EN COURS » ou « en cours »
     * tombe sur la meme colonne.
     */
    private function referencesImport(?int $projetDefaut = null): object
    {
        $societe = company()->id;

        $projets = DB::table('projects')
            ->whereNull('deleted_at')->where('company_id', $societe)
            ->select('id', 'project_name', 'project_short_code')->get()
            ->keyBy(fn($p) => $this->normaliser($p->project_name));

        $gens = DB::table('users')
            ->where('company_id', $societe)->where('status', 'active')
            ->select('id', 'name', 'email')->get();

        $parNom = $gens->keyBy(fn($u) => $this->normaliser($u->name));
        $parMail = $gens->keyBy(fn($u) => $this->normaliser($u->email));

        $categories = DB::table('task_category')
            ->where('company_id', $societe)
            ->select('id', 'category_name')->get()
            ->keyBy(fn($c) => $this->normaliser($c->category_name));

        /* Un statut se reconnait a son libelle ou a son identifiant
           technique : « À faire » comme « to_do ». */
        $statuts = [];

        foreach (TaskboardColumn::all() as $col) {
            $statuts[$this->normaliser($col->column_name)] = $col;
            $statuts[$this->normaliser($col->slug)] = $col;
        }

        /* Les taches deja en base, sous la meme empreinte que celle qui
           sert aux doublons internes au fichier.

           Sans ce garde-fou, rejouer un fichier deja importe recreait
           tout en silence — ce qui arrive des qu'un import parait avoir
           echoue alors qu'il avait reussi. */
        $existantes = [];

        $lignesBase = DB::table('tasks')
            ->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.company_id', $societe)
            ->select('tasks.heading', 'projects.project_name')->get();

        foreach ($lignesBase as $t) {
            $existantes[$this->normaliser($t->heading . '|' . ($t->project_name ?? ''))] = true;
        }

        return (object) [
            'projets'    => $projets,
            'gensNom'    => $parNom,
            'gensMail'   => $parMail,
            'categories' => $categories,
            'statuts'    => $statuts,
            'existantes' => $existantes,
            'projetDefaut' => $projetDefaut ? ($projets->firstWhere('id', $projetDefaut) ?: null) : null,
            'defaut'     => TaskboardColumn::where('slug', 'incomplete')->first(),
        ];
    }

    private function analyserLigne(array $brute, array $index, object $refs, int $numero, array &$vus): object
    {
        $lire = fn(string $cle) => $index[$cle] === null
            ? '' : trim((string) ($brute[$index[$cle]] ?? ''));

        $erreurs = [];

        $titre = $lire('heading');

        if ($titre === '') {
            $erreurs[] = sprintf(__('Ligne %d : la tâche n\'a pas de titre.'), $numero);
        }

        /* Projet.

           Resolu avant tout le reste : c'est lui qui distingue deux
           taches de meme intitule, l'empreinte des doublons a donc
           besoin de sa valeur definitive — celle du fichier, ou celle
           choisie a l'ecran d'import quand la colonne est vide. */
        $projet = null;
        $nomProjet = $lire('project');

        if ($nomProjet !== '') {
            $projet = $refs->projets[$this->normaliser($nomProjet)] ?? null;

            if (!$projet) {
                $erreurs[] = sprintf(__('Ligne %d : le projet « %s » n\'existe pas.'), $numero, $nomProjet);
            }
        }
        elseif ($refs->projetDefaut) {
            $projet = $refs->projetDefaut;
            $nomProjet = $projet->project_name;
        }

        /* Empreinte batie sur le nom reel du projet, et non sur ce que
           porte la cellule : deux lignes rattachees au meme projet — que
           ce soit par la colonne ou par defaut — sont bien un doublon. */
        $empreinte = $this->normaliser($titre . '|' . ($projet->project_name ?? ''));

        if ($titre !== '' && isset($vus[$empreinte])) {
            $erreurs[] = sprintf(
                __('Ligne %d : doublon de la ligne %d (même tâche, même projet).'),
                $numero, $vus[$empreinte]
            );
        }
        elseif ($titre !== '') {
            $vus[$empreinte] = $numero;

            /* Doublon avec une tache deja enregistree. */
            if (isset($refs->existantes[$empreinte])) {
                $erreurs[] = sprintf(
                    __('Ligne %d : la tâche « %s » existe déjà%s. Supprimez cette ligne, ou renommez la tâche.'),
                    $numero,
                    $titre,
                    $nomProjet !== ''
                        ? sprintf(__(' dans le projet « %s »'), $nomProjet) : ''
                );
            }
        }

        /* Categorie */
        $categorie = null;
        $nomCategorie = $lire('category');

        if ($nomCategorie !== '') {
            $categorie = $refs->categories[$this->normaliser($nomCategorie)] ?? null;

            if (!$categorie) {
                $erreurs[] = sprintf(__('Ligne %d : le domaine « %s » n\'existe pas.'), $numero, $nomCategorie);
            }
        }

        /* Statut : celui du fichier, ou « Incomplète » par defaut. */
        $statut = $refs->defaut;
        $nomStatut = $lire('status');

        if ($nomStatut !== '') {
            $trouve = $refs->statuts[$this->normaliser($nomStatut)] ?? null;

            if ($trouve) {
                $statut = $trouve;
            }
            else {
                $erreurs[] = sprintf(__('Ligne %d : le statut « %s » n\'est pas reconnu.'), $numero, $nomStatut);
            }
        }

        /* Collaborateurs : plusieurs noms separes par une virgule ou un
           point-virgule, reconnus par nom ou par adresse. */
        $gens = [];
        $nomsGens = [];
        $brutGens = $lire('assigned');

        if ($brutGens !== '') {
            foreach (preg_split('/[;,]/', $brutGens) as $morceau) {
                $morceau = trim($morceau);

                if ($morceau === '') { continue; }

                $cle = $this->normaliser($morceau);
                $u = $refs->gensNom[$cle] ?? $refs->gensMail[$cle] ?? null;

                if ($u) {
                    $gens[] = $u->id;
                    $nomsGens[] = $u->name;
                }
                else {
                    $erreurs[] = sprintf(
                        __('Ligne %d : le collaborateur « %s » est introuvable.'), $numero, $morceau);
                }
            }
        }

        /* Dates */
        $debut = $this->lireDate($lire('start_date'));
        $fin = $this->lireDate($lire('due_date'));

        if ($lire('start_date') === '') {
            $erreurs[] = sprintf(__('Ligne %d : la date de début est obligatoire.'), $numero);
        }
        elseif (!$debut) {
            $erreurs[] = sprintf(
                __('Ligne %d : la date de début « %s » est illisible.'), $numero, $lire('start_date'));
        }

        if ($lire('due_date') !== '' && !$fin) {
            $erreurs[] = sprintf(
                __('Ligne %d : la date de fin « %s » est illisible.'), $numero, $lire('due_date'));
        }

        if ($debut && $fin && $fin->lt($debut)) {
            $erreurs[] = sprintf(
                __('Ligne %d : la date de fin est antérieure à la date de début.'), $numero);
        }

        return (object) [
            'numero'      => $numero,
            'titre'       => $titre,
            'description' => $lire('description'),
            'projet'      => $projet,
            'projetNom'   => $nomProjet,
            'categorie'   => $categorie,
            'statut'      => $statut,
            'gens'        => $gens,
            'gensNoms'    => $nomsGens,
            'debut'       => $debut,
            'fin'         => $fin,
            'erreurs'     => $erreurs,
        ];
    }

    /**
     * Lit une date au format de la societe, aux formats courants, ou au
     * numero de serie d'Excel — une cellule formatee en date arrive
     * souvent sous forme de nombre.
     */
    private function lireDate($valeur): ?Carbon
    {
        $valeur = trim((string) $valeur);

        if ($valeur === '') {
            return null;
        }

        if (is_numeric($valeur) && (float) $valeur > 20000) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $valeur)
                )->startOfDay();
            }
            catch (\Throwable $e) {
                return null;
            }
        }

        $formats = array_unique([
            company()->date_format, 'd/m/Y', 'd-m-Y', 'Y-m-d', 'd.m.Y', 'm/d/Y',
        ]);

        foreach ($formats as $f) {
            try {
                $d = Carbon::createFromFormat($f, $valeur);

                if ($d && $d->format($f) === $valeur) {
                    return $d->startOfDay();
                }
            }
            catch (\Throwable $e) {
                // format suivant
            }
        }

        return null;
    }

    /** Sans accent, sans casse, sans espace superflu. */
    private function normaliser($valeur): string
    {
        $v = (string) $valeur;

        /* Excel prefixe ses « CSV UTF-8 » d'une marque d'ordre des
           octets. Invisible a l'ecran, elle colle a la premiere en-tete
           et la rend introuvable. */
        $v = preg_replace('/^ï»¿/', '', $v);

        /* Espaces insecables compris : un copier-coller depuis le web en
           seme, et « Date de début » cesse alors de correspondre. */
        $v = str_replace(["Â ", "â¯"], ' ', $v);

        $v = Str::ascii(trim($v));

        return strtolower(preg_replace('/\s+/', ' ', $v));
    }

    /**
     * Enregistre les taches analysees, en une seule transaction.
     *
     * Rien n'est ecrit si une seule ligne echoue : la demande est de ne
     * jamais laisser un fichier a moitie importe.
     *
     * @return int nombre de taches creees
     */
    protected function creerTachesImportees(array $lignes): int
    {
        $attente = TaskboardColumn::waitingForApprovalColumn();
        $creees = 0;

        /* Les taches creees et leurs destinataires, retenus pour
           notifier une fois la transaction close : un courriel parti
           depuis une transaction qui echoue serait un mensonge. */
        $aNotifier = [];

        DB::transaction(function () use ($lignes, $attente, &$creees, &$aNotifier) {
            foreach ($lignes as $l) {
                $task = new Task();
                $task->heading = $l->titre;
                $task->description = $l->description !== '' ? $l->description : null;
                $task->start_date = $l->debut;
                $task->due_date = $l->fin;
                $task->project_id = $l->projet->id ?? null;
                $task->task_category_id = $l->categorie->id ?? null;
                $task->priority = 'medium';
                $task->board_column_id = $l->statut->id;

                /* TaskObserver::creating() ecrase board_column_id par le
                   statut par defaut du cabinet des que la requete ne
                   porte pas ce parametre — le statut lu dans le fichier
                   etait donc perdu, et toutes les taches importees
                   atterrissaient dans la meme colonne.

                   On emprunte le canal que l'observer attend, celui-la
                   meme qu'utilise le tableau kanban, plutot que de
                   corriger la ligne apres coup : la valeur est ainsi
                   posee avant l'insertion, et non rattrapee ensuite. */
                request()->merge(['board_column_id' => $l->statut->id]);
                $task->approval_send = $l->statut->id === $attente->id ? 1 : 0;
                $task->is_private = 0;
                $task->billable = 0;

                /* Meme reference que la creation manuelle : prefixe du
                   projet suivi d'un compteur, ou simple rang. */
                if ($l->projet) {
                    $rang = Task::projectTaskCount($l->projet->id);

                    $task->task_short_code = $l->projet->project_short_code
                        ? $l->projet->project_short_code . '-' . $this->getTaskShortCode(
                            $l->projet->project_short_code, $rang + 1)
                        : $rang + 1;
                }

                $task->save();

                if ($l->gens) {
                    $task->users()->sync($l->gens);
                }

                /* Historique.

                   TaskObserver l'ecrit deja, mais uniquement hors
                   contexte console : le bloc entier est enveloppe dans
                   « if (!isRunningInConsoleOrSeeding()) ». On ne peut
                   donc ni s'y fier depuis une commande, ni le verifier
                   par un test automatise, qui s'execute toujours en CLI.

                   On l'ecrit ici, apres avoir verifie qu'il ne l'a pas
                   deja fait : la tache porte alors la meme trace, qu'elle
                   vienne du formulaire ou d'un fichier. */
                $dejaTrace = DB::table('task_history')
                    ->where('task_id', $task->id)
                    ->exists();

                if (!$dejaTrace) {
                    DB::table('task_history')->insert([
                        'task_id'         => $task->id,
                        'user_id'         => user()->id,
                        'board_column_id' => $task->board_column_id,
                        'details'         => 'createActivity',
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }

                if ($l->gens) {
                    $aNotifier[] = ['task' => $task, 'gens' => $l->gens];
                }

                $creees++;
            }
        });

        /* La requete est rendue telle qu'on l'a trouvee : le parametre
           n'a servi qu'a traverser l'observer. */
        request()->request->remove('board_column_id');

        $this->notifierAffectations($aNotifier);

        return $creees;
    }

    /**
     * Previent les collaborateurs des taches qui leur sont confiees.
     *
     * Un seul recapitulatif par personne, et non un message par tache.
     * La mesure impose ce choix : un envoi SMTP coute neuf secondes sur
     * ce serveur, et la file d'attente est en « sync ». Notifier tache
     * par tache aurait fait durer un import de vingt lignes trois
     * minutes — au-dela du delai maximal de PHP.
     *
     * A noter : la creation manuelle, elle, ne notifie plus du tout,
     * l'appel de TaskObserver ayant ete commente lors d'un correctif sur
     * les doublons. Une tache importee previent donc son destinataire,
     * ce qu'une tache saisie a la main ne fait pas.
     *
     * @param array $lots [['task' => Task, 'gens' => int[]], ...]
     */
    private function notifierAffectations(array $lots): void
    {
        if (!$lots) {
            return;
        }

        /* Regroupement par collaborateur. Une tache privee ne notifie
           personne : c'est la regle qu'applique deja TaskListener. */
        $parPersonne = [];

        foreach ($lots as $lot) {
            if ($lot['task']->is_private) {
                continue;
            }

            foreach ($lot['gens'] as $id) {
                /* On ne se previent pas soi-meme. */
                if (user() && $id === user()->id) {
                    continue;
                }

                $parPersonne[$id][] = $lot['task'];
            }
        }

        if (!$parPersonne) {
            return;
        }

        /* Les destinataires en une seule lecture, et non un par un. */
        $gens = User::whereIn('id', array_keys($parPersonne))->get()->keyBy('id');

        foreach ($parPersonne as $id => $taches) {
            if (!isset($gens[$id])) {
                continue;
            }

            /* Un envoi qui echoue ne doit pas defaire l'import.

               Les taches sont deja enregistrees quand on arrive ici :
               laisser remonter l'exception afficherait une erreur pour
               un import pourtant reussi, et l'utilisateur rejouerait
               son fichier — c'est ainsi qu'on a fabrique des doublons.

               Le serveur refuse par exemple les adresses desactivees
               (« 525 Disabled recipient address »). On le consigne et
               l'on poursuit : la notification en base, elle, est
               ecrite quoi qu'il arrive. */
            try {
                Notification::send($gens[$id], new TachesImportees(collect($taches)));
            }
            catch (\Throwable $e) {
                Log::warning('Import de taches : notification non delivree a l\'utilisateur '
                    . $id . ' — ' . $e->getMessage());
            }
        }
    }

}

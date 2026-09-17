# Rapport — fichiers candidats au nettoyage (anciens modules)

Date : 2026-09-16. Branche : `test/non-regression-modules-conserves`.
Méthode : cartographie par exploration de code + vérification empirique
(`php artisan route:list` réel, grep de sécurité) — pas de suppositions
basées sur le seul nom de fichier ou de module.

Aucune suppression n'a été exécutée. Les commandes `php artisan
audit:files-preview` (lecture seule) et `php artisan audit:files-delete
--confirm` (destructif, whitelist figée dans `AuditFilesPreview::WHITELIST`)
sont prêtes dans `app/Console/Commands/`.

## Candidats sûrs (whitelist active)

Familles pour lesquelles aucune référence n'a été trouvée depuis les modules
conservés (Tâches, Projets, Employés, Contrats, Clients, Paramètres,
Dashboard), et aucune route live (vérifié via `route:list` réel, pas
seulement une lecture statique des fichiers de routes) :

| Famille | Fichiers | Preuve d'absence de référence | Risque |
|---|---|---|---|
| Notices | `NoticeController`, `NoticeFileController`, observers, events, listener, modèles `Notice*`, `NoticeBoardDataTable`, vues `resources/views/notices` | 0 route live (web.php, web-settings.php, api.php) ; le widget dashboard qui l'affichait ne fournissait déjà plus la variable `$notices` (retiré à l'étape B.1) | Faible |
| Awards | `AwardController`, observer, modèles `Award`, `AwardIcon`, `AwardDataTable`, vues `resources/views/awards` | 0 route live | Faible |
| Appreciation | `AppreciationController`, observer, event, listener, modèle `Appreciation`, 2 DataTables, vues `resources/views/appreciations` | 0 route live | Faible |
| Modules/Recruit | Module nwidart complet (~50 fichiers) | 0 référence `Modules\Recruit` dans `app/`, `routes/`, `resources/views` ; 0 route live détectée par `route:list` réel ; **les 43 tables de ses migrations n'existent même pas dans la base de dev actuelle** (`php artisan audit:tables-preview`) | Faible |
| Modules/Sms | Module nwidart complet | Idem : 0 référence, 0 route live, tables absentes de la base | Faible |
| Orders / Products (commerciaux) | `OrderController`, `Product*Controller`, observers, event/listener `OrderUpdated*`, modèles `Order*`/`Product*` (hors `Promotion.php`, sans rapport malgré le nom), 2 DataTables, vues `resources/views/orders` et `products` | 0 route live ; 0 référence depuis un contrôleur conservé ; **0 commande planifiée dans `app/Console/Kernel.php` n'en dépend** (vérifié un par un, seule famille couplée à avoir passé ce test) | Faible |
| Award / Appreciation (contrôleurs/observer/DataTables uniquement) | `AwardController`, `AppreciationController`, leurs DataTables, vues `resources/views/awards`/`appreciations` | 0 route live | Faible — **mais `app/Models/Award.php`, `AwardIcon.php`, `Appreciation.php` en sont explicitement exclus** (voir correction ci-dessous) |

**Prérequis appliqué avant cette liste** : `CompanyObserver::created()` et `UserObserver::saving()` ne référencent plus Lead/Ticket/Leave/Attendance (sinon la création d'une société ou la désactivation d'un employé aurait planté). Les entrées `$observers` correspondantes ont été retirées de `app/Providers/EventServiceProvider.php`. **Point de vigilance découvert cette session** : ce tableau est lu et exécuté à *chaque requête* — toute suppression de fichier Observer doit obligatoirement retirer l'entrée correspondante ici en même temps, sous peine de faire planter l'application entière (pas juste une page). Notices/Awards/Appreciation avaient été ajoutées à la whitelist fichiers lors d'un tour précédent sans que leurs entrées `$observers` (`Notice`, `NoticeFile`, `NoticeView`, `Award`, `Appreciation`) aient été retirées — corrigé cette session, jamais exécuté avec `--confirm` donc aucun dégât réel.

Le grep de sécurité intégré à `audit:files-preview` signale presque tous
ces fichiers comme "référence trouvée" : dans l'immense majorité des cas il
s'agit d'un `use` d'import non retiré dans `routes/web.php` dont toutes les
routes réelles sont déjà commentées (vérifié manuellement, cf. ligne
`use App\Http\Controllers\NoticeController;` à la ligne 13 de `routes/web.php`,
sans aucune route active en dessous). Ce n'est **pas** un blocage fonctionnel,
mais à vérifier à l'oeil avant `--confirm`, le grep n'étant volontairement
pas plus intelligent qu'un usage direct par nom de classe.

## Correction critique (2026-09-16, après relecture par un second agent)

`app/Models/Award.php`, `app/Models/AwardIcon.php` et `app/Models/Appreciation.php`
avaient été inclus dans une version précédente de la whitelist, classés
"Award/Appreciation" comme isolés au même titre que Notice. **C'était faux** :
`EmployeeController.php:682-887` (onglet « Appréciations » de la fiche
employé, `case 'appreciation':` puis méthode `appreciation()`) interroge
directement `Appreciation::with(['award', 'award.awardIcon', 'awardTo'])`
— une fonctionnalité active du module Employés conservé. Corrigé : les 3
modèles retirés de la whitelist fichiers, `awards`/`award_icons`/
`appreciations` retirées de la whitelist tables, `Award::class`/
`Appreciation::class` restaurés dans `EventServiceProvider.php`. Jamais
exécuté avec `--confirm`, aucun dégât réel — mais un rappel que même une
famille qui semble "isolée" au premier passage peut avoir un point d'usage
caché dans un contrôleur conservé.

## Candidate bloquée — ne pas mettre dans la whitelist

| Famille | Raison |
|---|---|
| KnowledgeBase (`KnowledgeBaseController`, `KnowledgeBaseCategoryController`, `KnowledgeBaseFileController`, modèles, vues) | `routes/api.php` sert encore `/api/knowledge-base` via `AuthController::getKnowledgeBase()` (API mobile). Les contrôleurs web n'ont aucune route live, mais le modèle `KnowledgeBase` et ses tables sont encore utilisés par ce point d'entrée. Retirer les contrôleurs web seuls pourrait être envisagé dans un tour dédié, après vérification précise de ce que `getKnowledgeBase()` utilise réellement. |

## Familles couplées — hors périmètre de ce tour

Les onglets/widgets qui appelaient ces familles depuis les pages
Client/Projet/Employé/Dashboard ont été retirés (étape B.1), et la plupart
de leurs routes étaient déjà commentées par un travail antérieur. Mais
**elles restent hors whitelist de suppression de fichiers**, pour deux
raisons distinctes découvertes cette session :

1. **Couplage direct avec un module conservé** :
   - `ContractSettingController` (Contrats) stocke ses réglages de
     numérotation dans `invoice_settings`. `InvoiceSetting` sert aussi de
     source de la locale, chargée par `AccountBaseController` pour
     **tous** les contrôleurs — ce n'est pas une table du module Invoice,
     c'est une infrastructure partagée. `Invoice.php`/`invoices` (la table
     de données) est en plus directement interrogée par
     `ProjectController::invoiceList()`.
   - `Lead.php`/`LeadObserver.php` : conversion lead→client active dans
     `ClientController`.
   - `Promotion.php` (avancement RH) partage juste un nom avec les
     "promotions" commerciales — aucun rapport, module Employés conservé.
   - `AutomateShift.php` et toute la famille `EmployeeShift*` : utilisés
     par `EmployeeController` (attribution groupée d'horaires) et créés par
     `CompanyObserver::employeeShift()`.

2. **Couplage invisible depuis les routes : commandes planifiées
   (`app/Console/Kernel.php`)**. C'est la découverte la plus importante de
   cette session — un premier essai d'élargissement de la whitelist
   (Leads/Deals-secondaires, Invoices/Estimates/CreditNotes, Tickets,
   Leave/Attendance, ShiftRotation) a dû être **entièrement annulé** après
   avoir constaté que ces commandes, exécutées par cron indépendamment de
   toute route HTTP, utilisent activement ces mêmes classes :

   | Commande planifiée | Famille touchée |
   |---|---|
   | `CarryForwardLeaves`, `LeavesQuotaRenew`, `CreateEmployeeLeaveQuotaHistory` | Leave |
   | `SendAttendanceReminder`, `SendMonthlyAttendanceReport` | Attendance |
   | `AutoCreateRecurringInvoices`, `SendInvoiceReminder` | Invoice (recurring) |
   | `FetchTicketEmails` | Ticket |
   | `AssignShiftRotation`, `AssignEmployeeShiftRotation` | ShiftRotation |
   | `SendAutoFollowUpReminder` | `DealFollowUp` (famille Leads/Deals) |

   Seule **Orders/Products** a été vérifiée sans dépendance dans
   `app/Console/Kernel.php` (liste complète des ~26 commandes planifiées
   passée en revue) et a donc pu être ajoutée à la whitelist ci-dessus.

**Recommandation pour un tour futur** : avant d'élargir encore la
whitelist, croiser systématiquement chaque commande de
`app/Console/Kernel.php` (26 commandes au total) avec chaque famille
candidate — pas seulement les routes et les contrôleurs conservés.

## Découverte majeure (cartographie complète, second agent) — Tableau de bord avancé

Un audit complet des 254 tables du projet confirme que **le Tableau de
bord avancé (module conservé) dépend massivement des anciens modules** via
les traits `app/Traits/ClientDashboard.php`, `FinanceDashboard.php`,
`HRDashboard.php`, `OverviewDashboard.php`, `TicketDashboard.php` (seul
`ProjectDashboard.php` est propre). Ces traits interrogent directement
`Invoice`, `Estimate`, `Proposal`, `Expense`, `Payment`, `Ticket`,
`TicketChannel`, `TicketType`, `Deal`, `Lead`, `LeadPipeline`, `LeadSource`,
`Leave`, `Attendance`, `Notice`. **Toute suppression future de ces familles
doit d'abord retirer/adapter ces traits et leurs onglets**, sans quoi le
Tableau de bord avancé affichera des erreurs SQL. Ce point n'a pas été
traité dans cette session (découvert après coup par l'agent de
cartographie) — à traiter avant d'aller plus loin sur Ticket/Invoice/Lead/
Leave/Attendance.

Le même audit signale aussi : des tables sans migration correspondante en
base (`radiation_requests`, `situations_fiscale`, `situations_sociale`,
`entreprises` — rattachées au module Client conservé malgré l'absence de
migration), une anomalie de nommage table singulier/pluriel
(`etatfinancier` vs `etatfinanciers`, `formulaire` vs `formulaires`), et des
tables de packages Laravel visiblement inutilisés (`subscriptions`
/Cashier, `push_subscriptions`, `conversation`/`conversation_reply`).
Détail complet dans le rapport de l'agent, non repris ici faute de temps
dans cette session — à valider avant toute action.

## Découverte annexe (hors périmètre, à signaler)

`routes/api.php` route trois autres endpoints vers `AuthController` et
touchent des modules à retirer : `getInvoices`, `getAttendances`,
`getKnowledgeBase` (API mobile). Cette API n'a pas été auditée dans ce tour
(hors du plan approuvé) — à examiner séparément avant toute suppression
touchant Invoice/Attendance/KnowledgeBase.

# Rapport — tables candidates au nettoyage (anciens modules)

Date : 2026-09-16. Base auditée : `tacheflux` (dev, `.env` courant), via
`php artisan audit:tables-preview` (lecture seule, `information_schema`
uniquement, aucun `DROP`).

Aucune table n'a été supprimée. `php artisan audit:tables-delete --confirm
--i-have-a-backup` (whitelist figée dans `AuditTablesPreview::WHITELIST`)
est prêt mais n'a pas été exécuté.

## Résultat de l'exécution réelle (2026-09-16)

| Table | Lignes | Clé étrangère entrante | Décision |
|---|---|---|---|
| `notices` | 0 | — | Candidate |
| `notice_files` | 0 | — | Candidate |
| `notice_board_users` | 0 | — | Candidate |
| `notice_views` | 0 | — | Candidate |
| `awards` | 0 | référencée par `appreciations.award_id` (même famille, supprimée ensemble) | Candidate |
| `award_icons` | 0 | référencée par `awards.award_icon_id` (même famille) | Candidate |
| `appreciations` | 0 | — | Candidate |
| 43 tables `recruit_*` / `application_sources` / `job_interview_stages` / `offer_letter_histories` / `recruiters` | — | **Table absente de la base actuelle** | Rien à supprimer ici ; whitelist conservée pour un environnement où ces migrations auraient tourné |
| `sms_settings`, `sms_notification_settings` | — | **Absentes de la base actuelle** | Idem |
| `orders`, `order_items`, `order_item_images`, `products`, `product_category`, `product_sub_category`, `product_files` | 0 | `orders` référencée par `invoices.order_id` ; `products` référencée par 8 tables (`credit_note_items`, `estimate_items`, `estimate_template_items`, `invoice_items`, `invoice_recurring_items`, `lead_products`, `proposal_items`, `proposal_template_items`) — toutes **hors whitelist**, donc conservées | `audit:tables-delete` **refusera automatiquement** `orders`/`products`/`product_category`/`product_sub_category` (clé étrangère externe détectée) ; seules `order_items`, `order_item_images`, `product_files` se supprimeraient réellement |

Toutes les tables Notices/Awards/Appreciation existantes sont **vides (0
ligne)** : aucune perte de données réelle si elles sont supprimées. Les 45
tables Recruit/Sms n'existent tout simplement pas dans cette base — leur
présence dans la whitelist documente ce qu'il faudrait nettoyer si ces
modules avaient un jour été migrés, sans risque immédiat ici.

Les clés étrangères entre `awards`/`award_icons`/`appreciations` sont
internes à la même famille : `audit:tables-delete` les traite comme un
groupe (il refuse une table si elle est référencée par une table **hors**
whitelist, pas par une autre entrée de la même whitelist).

## Tables bloquées — hors whitelist

| Table | Raison |
|---|---|
| `invoice_settings` | `ContractSettingController` (module Contrats, conservé) y stocke `contract_prefix`, `contract_number_separator`, `contract_digit` ; sert aussi de source de la locale pour tous les contrôleurs (`AccountBaseController`). Infrastructure partagée, pas une table du module Invoice. |
| `invoices`, tables `invoice_recurring*` | `invoices` interrogée directement par `ProjectController::invoiceList()` ; `invoice_recurring*` alimentée par la commande planifiée `AutoCreateRecurringInvoices` (`app/Console/Kernel.php`, cron, indépendant des routes web). |
| `leaves`, `leave_types`, `leave_settings`, `attendances`, `attendance_settings` | Alimentées par les commandes planifiées `CarryForwardLeaves`, `LeavesQuotaRenew`, `CreateEmployeeLeaveQuotaHistory`, `SendAttendanceReminder`, `SendMonthlyAttendanceReport`. |
| `tickets` et tables associées | Alimentées par la commande planifiée `FetchTicketEmails` (récupération des emails entrants). |
| `employee_shift_rotations`, `shift_rotation_sequences` | Utilisées par les commandes planifiées `AssignShiftRotation`/`AssignEmployeeShiftRotation`, et par `AutomateShift` (attribution groupée d'horaires, `EmployeeController`, module Employés conservé). |
| `leads`, `deal_follow_up` | `leads` : conversion lead→client active (`ClientController`) ; `deal_follow_up` : alimentée par la commande planifiée `SendAutoFollowUpReminder`. |
| `estimates`, `credit_notes` et tables associées | Aucune dépendance active trouvée (ni route, ni contrôleur conservé, ni commande planifiée) — mais laissées prudemment groupées avec le reste de la famille Invoice tant qu'un tour dédié n'a pas isolé leur cas précisément. |
| `knowledge_base*` | Encore utilisée par `/api/knowledge-base` (API mobile, `AuthController`). |

**Découverte de cette session** : un premier essai d'élargir la whitelist à
ces familles a été annulé après avoir croisé chaque commande de
`app/Console/Kernel.php` (26 commandes planifiées) avec les tables
candidates. Voir `docs/audit/fichiers-inutiles.md` pour le détail
commande → famille.

## Procédure avant `--confirm --i-have-a-backup`

1. Sauvegarder la base (`mysqldump` ou équivalent) — la commande ne le fait
   pas elle-même et ne vérifie pas techniquement qu'une sauvegarde existe.
2. Relancer `audit:tables-preview` pour confirmer que l'état n'a pas changé.
3. Exécuter `audit:tables-delete --confirm --i-have-a-backup` (une
   confirmation interactive supplémentaire sera demandée).
4. Vérifier les 6 modules conservés (tâches, projets, employés, contrats,
   clients, paramètres, dashboard) après coup.
5. En cas d'anomalie : restauration uniquement depuis la sauvegarde de
   l'étape 1 — une migration `down()` ne peut pas recréer les données
   supprimées.

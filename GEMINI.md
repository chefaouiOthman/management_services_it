Act as an Elite Enterprise Laravel 10 Software Architect and Senior Full-Stack Engineer. Our objective is to build, review, and perfectly synchronize a highly complex ERP ecosystem composed of 6 interdependent modules. 

The project incorporates Laravel Breeze for authentication, Spatie Laravel-Permission for ACL (Roles/Permissions), and demands absolute structural consistency. 

To eliminate any risk of bugs, typos, mass-assignment exceptions, or foreign key constraint failures during migrations, you must follow these absolute structural rules:
1. FIELD NAME CONSISTENCY: Every field name must match exactly across the Migration, the Model ($fillable), the Controller validation rules, and Form Requests.
2. TOTAL CRUD COMPLETENESS: Every controller must implement all 7 standard RESTful actions (index, create, store, show, edit, update, destroy). No method should be left as a placeholder or comment.
3. TRANSACTIONAL INTEGRITY: Use DB::transaction() in store/update actions when managing operations that touch multiple tables or pivots to prevent partial data corruption.
4. ORDER OF MIGRATIONS: Define database tables in strict dependency order so that no foreign key references a table that has not yet been created.
5. SPATIE MIDDLEWARE INTEGRITY: Every route must be mapped with explicit Spatie permission middlewares using a strict naming convention (e.g., 'permission:manage-assets').

Here is the finalized structural specification for our system's database schema, relations, and modules:

=========================================
CORE ARCHITECTURAL AND DATA DICTIONARY SPECIFICATIONS
=========================================

----------------------------------------------------
MODULE 1: HUMAIN (AUTHENTICATION & PROFILE POLYMORPHISM)
----------------------------------------------------
- User:
  * Table: `users`
  * Fields: `id` (BigInt PK Auto-Increment), `email` (Varchar 255 Unique), `password` (Varchar 255), `nom_complet` (Varchar 150), `est_actif` (Boolean, default: true), `timestamps`.
- Employe (Identitarian One-to-One Inheritance):
  * Table: `employes`
  * Fields: `user_id` (BigInt PK & FK pointing to `users.id`, WITHOUT auto-increment), `date_embauche` (Date), `CIN` (Varchar 50 Unique), `timestamps`.
- Stagiaire (Identitarian One-to-One Inheritance):
  * Table: `stagiaires`
  * Fields: `user_id` (BigInt PK & FK pointing to `users.id`, WITHOUT auto-increment), `ecole_origine` (Varchar 150), `sujet_stage` (Text), `timestamps`.
- Client (Identitarian One-to-One Inheritance):
  * Table: `clients`
  * Fields: `user_id` (BigInt PK & FK pointing to `users.id`, WITHOUT auto-increment), `type_client` (Enum: 'physique', 'morale'), `nom_societe` (Varchar 150 Nullable), `ice` (Varchar 50 Nullable), `timestamps`.

----------------------------------------------------
MODULE 2: RH, PRÉSENCES & SÉCURITÉ PHYSIQUE
----------------------------------------------------
- Departement:
  * Table: `departements`
  * Fields: `id` (BigInt PK), `nom_departement` (Varchar 100 Unique), `timestamps`.
- Contrat:
  * Table: `contrats`
  * Fields: `id` (BigInt PK), `employe_id` (BigInt FK to `employes.user_id`), `type_contrat` (Enum: 'CDI', 'CDD', 'Freelance'), `date_debut` (Date), `date_fin` (Date Nullable), `salaire_base` (Decimal 10,2), `heures_hebdo` (Integer), `statut` (Enum: 'actif', 'suspendu', 'termine'), `timestamps`.
- Pointage:
  * Table: `pointages`
  * Fields: `id` (BigInt PK), `user_id` (BigInt FK to `users.id`), `date_jour` (Date), `heure_arrivee` (DateTime), `heure_depart` (DateTime Nullable), `statut_presence` (Enum: 'a_l_heure', 'en_retard', 'depart_anticipe'), `timestamps`.
- Zone:
  * Table: `zones`
  * Fields: `id` (BigInt PK), `code_zone` (Varchar 50 Unique), `nom_salle` (Varchar 100), `niveau_requis` (Integer), `est_active` (Boolean, default: true), `timestamps`.
- HistoriquePassage:
  * Table: `historique_passages`
  * Fields: `id` (BigInt PK), `user_id` (BigInt FK to `users.id`), `zone_id` (BigInt FK to `zones.id`), `horodatage` (DateTime), `tentative_statut` (Enum: 'autorise', 'refuse_niveau_insuffisant', 'refuse_zone_desactivee'), `timestamps`.

----------------------------------------------------
MODULE 3: PRODUCTION & PROJETS
----------------------------------------------------
- Projet:
  * Table: `projets`
  * Fields: `id` (BigInt PK), `client_id` (BigInt FK to `clients.user_id`), `nom_projet` (Varchar 150), `description` (Text), `budget_vendu` (Decimal 12,2), `statut_projet` (Enum: 'analyse', 'developpement', 'recette', 'deploie', 'maintenance'), `timestamps`.
- Livrable:
  * Table: `livrabless`
  * Fields: `id` (BigInt PK), `projet_id` (BigInt FK to `projets.id`), `titre_jalon` (Varchar 150), `date_limite_soumission` (Date), `statut_client` (Enum: 'en_attente', 'rejete_avec_corrections', 'valide'), `timestamps`.
- Tache:
  * Table: `taches`
  * Fields: `id` (BigInt PK), `titre_tache` (Varchar 150), `timestamps`.
- FeuilleTemps:
  * Table: `feuille_temps`
  * Fields: `id` (BigInt PK), `employe_id` (BigInt FK to `employes.user_id`), `projet_id` (BigInt FK to `projets.id`), `date_effort` (Date), `duree_heures` (Decimal 4,2), `commentaire` (Text), `timestamps`.
- Technologie:
  * Table: `technologies`
  * Fields: `id` (BigInt PK), `nom_tech` (Varchar 50 Unique), `version` (Varchar 20), `timestamps`.

----------------------------------------------------
MODULE 4: FORMATIONS & PÉDAGOGIE
----------------------------------------------------
- CatalogueFormation:
  * Table: `catalogue_formations`
  * Fields: `id` (BigInt PK), `titre_formation` (Varchar 200 Unique), `description_programme` (Text), `prix_standard` (Decimal 10,2), `timestamps`.
- SessionFormation:
  * Table: `session_formations`
  * Fields: `id` (BigInt PK), `catalogue_formation_id` (BigInt FK to `catalogue_formations.id`), `date_debut` (Date), `date_fin` (Date), `salle_virtuelle` (Varchar 255 Nullable), `salle_concrete` (Varchar 255 Nullable), `timestamps`.
- Inscription:
  * Table: `inscriptions`
  * Fields: `id` (BigInt PK), `user_id` (BigInt FK to `users.id`), `session_formation_id` (BigInt FK to `session_formations.id`), `statut_inscription` (Enum: 'valide', 'annule', 'present', 'certifie'), `timestamps`.
- SupportCours:
  * Table: `support_cours`
  * Fields: `id` (BigInt PK), `nom_fichier` (Varchar 150), `url_stockage` (Varchar 255), `timestamps`.
- EvaluationSession:
  * Table: `evaluation_sessions`
  * Fields: `id` (BigInt PK), `session_formation_id` (BigInt FK to `session_formations.id`), `user_id` (BigInt FK to `users.id` - Student), `employe_id` (BigInt FK to `employes.user_id` - Trainer being evaluated), `note_pedagogie` (Integer), `note_technique` (Integer), `avis_textuel` (Text Nullable), `timestamps`.

----------------------------------------------------
MODULE 5: GESTION DES ACTIFS IT (ASSET MANAGEMENT)
----------------------------------------------------
- TypeMateriel:
  * Table: `type_materiels`
  * Fields: `id` (BigInt PK), `libelle_type` (Varchar 100 Unique), `description_type` (Text Nullable), `timestamps`.
- AssetMateriel:
  * Table: `asset_materiels`
  * Fields: `id` (BigInt PK), `type_materiel_id` (BigInt FK to `type_materiels.id`), `num_serie` (Varchar 100 Unique), `marque` (Varchar 100), `modele` (Varchar 100), `date_achat_actif` (Date Nullable), `statut_materiel` (Enum: 'disponible', 'attribue', 'en_panne', 'reforme'), `prix_achat` (Decimal 10,2 Nullable), `timestamps`.
- TicketMaintenance:
  * Table: `ticket_maintenances`
  * Fields: `id` (BigInt PK), `asset_materiel_id` (BigInt FK to `asset_materiels.id`), `user_id` (BigInt FK to `users.id` - Submitter, employee or trainee), `description_panne` (Text), `cout_reparation` (Decimal 10,2), `statut_ticket` (Enum: 'signale', 'en_atelier', 'resolu'), `timestamps`.
- LicenceLogiciel:
  * Table: `licence_logiciels`
  * Fields: `id` (BigInt PK), `nom_logiciel` (Varchar 100), `cle_licence` (Varchar 255), `date_expiration` (Date), `timestamps`.

----------------------------------------------------
MODULE 6: PILOTAGE FINANCIER & COMPTABILITÉ
----------------------------------------------------
- CategorieFlux:
  * Table: `categorie_flux`
  * Fields: `id` (BigInt PK), `libelle_categorie` (Varchar 100 Unique), `code_comptable` (Varchar 50 Nullable), `timestamps`.
- FluxTresorerie:
  * Table: `flux_tresoreries`
  * Fields: `id` (BigInt PK), `categorie_flux_id` (BigInt FK to `categorie_flux.id`), `type_mouvement` (Enum: 'entree', 'sortie'), `montant_operation` (Decimal 12,2), `date_comptable` (DateTime), `timestamps`.
- Facture:
  * Table: `factures`
  * Fields: `id` (BigInt PK), `client_id` (BigInt FK to `clients.user_id`), `flux_tresorerie_id` (BigInt FK to `flux_tresoreries.id` Nullable), `num_facture` (Varchar 50 Unique), `date_emission` (Date), `statut_paiement` (Enum: 'emise', 'en_retard_paiement', 'soldee'), `timestamps`.
- LigneFacture:
  * Table: `ligne_factures`
  * Fields: `id` (BigInt PK), `facture_id` (BigInt FK to `factures.id`), `designation` (Varchar 255), `quantite` (Decimal 10,2), `prix_unitaire_ht` (Decimal 10,2), `taux_tva` (Decimal 4,2), `timestamps`.
- FichePaie:
  * Table: `fiche_paies`
  * Fields: `id` (BigInt PK), `employe_id` (BigInt FK to `employes.user_id`), `flux_tresorerie_id` (BigInt FK to `flux_tresoreries.id` Nullable), `mois_annee` (Varchar 7), `net_a_payer` (Decimal 10,2), `timestamps`.
- NoteDeFrais:
  * Table: `note_de_frais`
  * Fields: `id` (BigInt PK), `employe_id` (BigInt FK to `employes.user_id`), `flux_tresorerie_id` (BigInt FK to `flux_tresoreries.id` Nullable), `motif_depense` (Varchar 255), `montant_ttc` (Decimal 10,2), `justificatif_path` (Varchar 255), `statut_remboursement` (Enum: 'soumis', 'approuve_manager', 'rejete', 'rembourse'), `timestamps`.

=========================================
MANY-TO-MANY (N,N) RECONFIGURED PIVOT TABLES SPECIFICATIONS
=========================================
Generate explicitly configured pivot table migrations and exact belongsToMany() methods with correct foreign keys for the following associations:

1. `projet_tache` (Pivot between Projet 1,N and Tache 1,N):
   * Columns: `projet_id` (FK), `tache_id` (FK) -> Composite PK.
   * Pivot Attributes: `priorite` (Enum: 'basse', 'moyenne', 'haute', 'bloquante'), `statut_tache` (Enum: 'backlog', 'en_cours', 'en_revue', 'termine').
2. `feuille_temps_tache` (Pivot between FeuilleTemps 1,N and Tache 1,N):
   * Columns: `feuille_temps_id` (FK), `tache_id` (FK) -> Composite PK.
3. `projet_technologie` (Pivot between Projet 1,N and Technologie 1,N):
   * Columns: `projet_id` (FK), `technologie_id` (FK).
4. `employe_session_formation` (Pivot between Employe 0,N acting as Trainer and SessionFormation 1,N):
   * Columns: `employe_id` (FK to `employes.user_id`), `session_formation_id` (FK to `session_formations.id`).
5. `catalogue_formation_support` (Pivot between CatalogueFormation 1,N and SupportCours 1,N):
   * Columns: `catalogue_formation_id` (FK), `support_cours_id` (FK).
6. `assignation_materiels` (Pivot between User 0,N and AssetMateriel 0,N):
   * Columns: `id` (PK), `user_id` (FK to `users.id`), `asset_materiel_id` (FK to `asset_materiels.id`).
   * Attributes: `date_remise` (Date), `date_restitution` (Date Nullable).
7. `assignation_licences` (Pivot between User 0,N and LicenceLogiciel 0,N):
   * Columns: `id` (PK), `user_id` (FK to `users.id`), `licence_logiciel_id` (FK to `licence_logiciels.id`).
   * Attributes: `date_attribution` (Date), `date_revocation` (Date Nullable).

=========================================
DATABASE SEEDING STRATEGY
=========================================
Create a Master DatabaseSeeder that orchestrates highly dense, realistic, chronological test data across all tables. 
- Volume target: SeedTest data should reflect a real system running for 2 years (e.g., generate multiple users, assigned assets with active and resolved maintenance tickets, interconnected time entries, cash flow histories derived from validated invoices/pay slips/expense reports).
- Ensure that the generated values strictly match the Enum lists and date ranges (e.g., `date_restitution` must be after `date_remise`).

=========================================
YOUR INSTRUCTIONS FOR CODE GENERATION
=========================================
Analyze the code currently present in the workspace. Perform a comprehensive review/generation across all layers. Output only complete, non-truncated PHP files for:
1. Migrations: Rewritten in strict execution order.
2. Models: Fully written with mass-assignment protections ($fillable), database casting ($casts), and exact mutual Eloquent relationships (belongsTo, hasMany, belongsToMany with explicit key naming).
3. Controllers: Standardized with complete, robust code implementing the 7 CRUD actions, Form validation rules, transactional blocks, and Spatie authorization queries.
4. Routes: Entirely updated inside `routes/web.php` organized in clean route groups wrapped under Spatie permission middlewares.

Do not skip fields or use comments like '// Code remains the same'. Provide the optimized output directly.
# Cahier des charges ReamMAR - CRM pour Mon Accompagnateur Rénov avec fonctionnalités CRM intégrées

## Contexte du projet

Le module ReamMAR est un CRM dédié au secteur des énergies renouvelables, conçu pour les auditeurs Mon Accompagnateur Rénov (MAR). Il s'intègre dans l'écosystème Numerimondes sous le namespace `Numerimondes\Modules\ReamMar\Core`.

Mon Accompagnateur Rénov est un service agréé par l'État, obligatoire depuis le 1er janvier 2024 pour bénéficier de certaines aides comme MaPrimeRénov' Parcours Accompagné. Il accompagne les ménages dans leurs projets de rénovation énergétique d'ampleur, en offrant des services de conseil, d'assistance financière (montage de dossiers d'aides), de suivi des travaux et d'accompagnement social dans les démarches administratives.

Le CRM intégré vient en complément des fonctionnalités principales pour gérer les prospects (leads) et les clients, tout en maintenant la priorité sur les missions MAR et la gestion des audits. Les fonctionnalités CRM permettent de suivre les interactions, de gérer les opportunités et d'automatiser certaines tâches administratives.

---

## Architecture technique

Le système est développé sous **Webkernel** avec **FilamentPHP v4 Beta** et **Laravel 12**, garantissant un développement rapide et une interface utilisateur intuitive. La base de données utilise des tables préfixées par `ream_mar_`, avec des colonnes en anglais. L'application est structurée en plusieurs parties, avec les fonctionnalités CRM ajoutées après les sections principales de gestion des missions et des clients.

---

## Partie 1 - Paramètres de l'entreprise (Settings)

Cette section regroupe les informations et configurations de l'entreprise de conseil, utilisées pour générer les contrats et gérer les prestations.

### Informations de l'entreprise (Table: ream_mar_company_settings)
- **company_name*** (string): Dénomination sociale
- **siret*** (string): Numéro SIRET
- **vat_rate*** (decimal:2): Taux de TVA
- **vat_number** (string): Numéro de TVA
- **address_line_1*** (string): Adresse ligne 1
- **address_line_2** (string): Adresse ligne 2
- **postal_code*** (string): Code postal
- **city*** (string): Ville
- **country*** (string): Pays
- **accreditation_number** (string): Numéro d'agrément
- **insurer_name** (string): Nom de l'assureur
- **insurance_policy_number** (string): Numéro de police d'assurance
- **insurance_certificate_path** (string): Chemin du fichier de l'attestation d'assurance
- **logo_path** (string): Chemin du fichier du logo de l'entreprise
- **stamp_path** (string): Chemin du fichier du tampon de l'entreprise
- **created_by** (integer): ID de l'utilisateur ayant créé l'enregistrement
- **updated_by** (integer): ID de l'utilisateur ayant mis à jour l'enregistrement

**Casts**:
```php
protected $casts = [
    'vat_rate' => 'decimal:2',
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
```

### Prestations proposées
- **offer_mar_service** (boolean): Proposer la prestation Mon Accompagnateur Rénov
  - **mar_certificate_path** (string): Chemin du justificatif MAR (obligatoire si activé)
- **offer_audit_service** (boolean): Proposer la prestation Auditeur
  - **audit_certificate_path** (string): Chemin du justificatif Auditeur (obligatoire si activé)

### Zone d'intervention
- **intervention_area** (text): Description de la zone d'intervention de l'entreprise

### Clauses optionnelles du contrat (Table: ream_mar_contract_clauses)
Permet de configurer des clauses optionnelles pour personnaliser les contrats générés pour chaque mission.
- **clause_name*** (string): Nom de la clause
- **price_ht** (decimal:2): Prix hors taxes
- **price_ttc** (decimal:2): Prix toutes taxes comprises (calculé avec TVA à 20%)
- **description** (text): Description de la clause
- **is_active_by_default** (boolean): Activer la clause par défaut
- **created_by** (integer): ID de l'utilisateur ayant créé l'enregistrement
- **updated_by** (integer): ID de l'utilisateur ayant mis à jour l'enregistrement

**Casts**:
```php
protected $casts = [
    'price_ht' => 'decimal:2',
    'price_ttc' => 'decimal:2',
    'is_active_by_default' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
```

**Fonctionnalité**:
- Les clauses peuvent être activées/désactivées pour chaque mission.
- Le contenu (titre, description, montant) est inséré dans le contrat via la variable `[OptionalClause]`.
- Possibilité d'ajouter, modifier ou supprimer des clauses via l'interface.

### Modèle de contrat MAR (Table: ream_mar_contract_templates)
- **template_content** (text): Contenu du modèle de contrat, incluant des variables entre `[crochets]` (ex. `[ClientName]`, `[OptionalClause]`)
- **is_default** (boolean): Indique si c'est le modèle par défaut
- **created_by** (integer): ID de l'utilisateur ayant créé l'enregistrement
- **updated_by** (integer): ID de l'utilisateur ayant mis à jour l'enregistrement

**Casts**:
```php
protected $casts = [
    'is_default' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
```

**Fonctionnalité**:
- Les variables sont automatiquement remplacées par les informations du dossier et du compte.
- Visualisation du contrat avec clauses avant envoi pour signature.

### Check Devis [Beta]
- Vérification automatique des mentions obligatoires de l'ANAH.
- Génération d'un email prérédigé pour signaler les modifications nécessaires.
- Envoi direct de l'email via l'application.
- **Contact** : Bouton pour rejoindre la bêta et faire des retours.

### Rapport annuel d'activité
- Génération d'un fichier Excel éditable listant les accompagnements :
  - Missions avec contrat signé par le MAR et le client.
  - Période : 1er janvier au 31 décembre de l'année écoulée.
  - Exclusion des missions archivées.
  - Exclusion des contrats ajoutés manuellement en 2025 pour des signatures de 2024.

---

## Partie 2 - Parcours de formulaires de Mission MAR

### 1. Création du contrat
- **Informations du demandeur** (via table `ream_mar_clients`):
  - **civility** (string): Civilité
  - **first_name** (string): Prénom
  - **last_name** (string): Nom
  - **address** (string): Adresse
  - **postal_code** (string): Code postal
  - **city** (string): Ville
  - **country** (string): Pays
  - **email** (string): Email
  - **phone** (string): Téléphone
- **Adresse du bien** (via table `ream_mar_biens`):
  - **address** (string): Rue
  - **postal_code** (string): Code postal
  - **city** (string): Ville
  - **country** (string): Pays
- **Type d'usage**:
  - **usage_type** (string): Résidence principale ou autre
- **Statut du ménage**:
  - **household_status** (string): Propriétaire occupant, bailleur, locataire, etc.
- **Rémunération forfaitaire** (via table `ream_mar_missions`):
  - **base_remuneration_ht** (decimal:2): Montant HT
  - **base_remuneration_ttc** (decimal:2): Montant TTC
  - **deposit_amount** (decimal:2): Premier acompte
- **Missions optionnelles**:
  - **mar_mandataire** (boolean): MAR mandataire administratif/financier
  - Ajout automatique des cerfa pour signature
- **Informations du ménage**:
  - **household_composition** (text): Composition du foyer
  - **fiscal_reference_income** (decimal:2): Revenu fiscal de référence
  - **household_category** (string): Catégorie du ménage
- **Mandat externe**:
  - **external_mandataire** (boolean): Mandataire administratif/financier externe

### 2. Visite initiale
- **Planification** (via table `ream_mar_visites`):
  - **visit_date** (date): Date
  - **start_time** (datetime): Heure de début
  - **duration** (integer): Durée
  - **additional_info** (text): Informations complémentaires
  - **participants** (array): Intervenants

### 3. Rendez-vous d'information préalable
- **Planification** (via table `ream_mar_visites`):
  - Mêmes champs que la visite initiale

### 4. Évaluation de l'état du logement
- **Travaux nécessaires** (via table `ream_mar_evaluations`):
  - **work_categories** (array): Dégradation, adaptation, insalubrité, etc.
- **Signalements**:
  - **unfit_housing** (boolean): Habitat indigne
  - **energy_indecency** (boolean): Indécence énergétique
  - **non_adaptation** (boolean): Non-adaptation
  - **resource_mismatch** (boolean): Inadaptation des ressources
- **Orientations**:
  - **enhanced_support** (boolean): Accompagnement renforcé
  - **adaptation_action** (boolean): Action d'adaptation

### 5. Audit énergétique
- **Options** (via table `ream_mar_audits`):
  - **audit_type** (string): Maître d'ouvrage, sous-traité, réalisé directement
- **Relevé**:
  - **survey_date** (date): Date du relevé
  - **survey_time** (datetime): Heure
  - **survey_duration** (integer): Durée
- **Restitution**:
  - **restitution_date** (date): Date
  - **restitution_time** (datetime): Heure
  - **restitution_type** (string): Type de restitution
  - **participants** (array): Intervenants
- **Rapport**:
  - **audit_report_path** (string): Chemin du rapport

### 6. Proposition Plan de Financement
- **Situation initiale** (via table `ream_mar_financements`):
  - **initial_energy_consumption** (decimal:2): Consommation énergétique
  - **initial_energy_class** (string): Classe énergétique
- **Travaux envisagés**:
  - **estimated_work_amount_ht** (decimal:2): Montant HT
  - **estimated_work_amount_ttc** (decimal:2): Montant TTC
  - **support_fees** (decimal:2): Frais d'accompagnement
- **Aides financières**:
  - **estimated_aids** (decimal:2): Aides estimées
  - **remaining_charge** (decimal:2): Reste à charge
- **Financement**:
  - **monthly_repayment_capacity** (decimal:2): Capacité de remboursement
  - **ecoptz_amount** (decimal:2): Montant ÉcoPTZ
  - **ecoptz_rate** (decimal:4): Taux
  - **ecoptz_duration_months** (integer): Durée
  - **ecoptz_monthly_payment** (decimal:2): Mensualité
  - **bank_loan_amount** (decimal:2): Montant prêt bancaire
  - **bank_loan_rate** (decimal:4): Taux
  - **bank_loan_duration_months** (integer): Durée
  - **bank_loan_monthly_payment** (decimal:2): Mensualité
- **Économies énergétiques**:
  - **energy_expenses_before** (decimal:2): Dépenses avant travaux
  - **energy_expenses_after** (decimal:2): Dépenses après travaux
  - **monthly_savings** (decimal:2): Économies mensuelles

### 7. Projet de Travaux
- **Devis** (via table `ream_mar_travaux`):
  - **work_category** (string): Catégorie des travaux
  - **description** (text): Description
  - **amount_ht** (decimal:2): Montant HT
  - **amount_ttc** (decimal:2): Montant TTC
  - **company** (string): Entreprise
  - **company_siret** (string): SIRET
  - **quote_path** (string): Chemin du devis
- **Synthèse audit** (via table `ream_mar_audits`):
  - **audit_date** (date): Date de réalisation
  - **audit_identifier** (string): Identifiant
  - **professional_siret** (string): SIRET du professionnel
  - **initial_energy_consumption_primary** (decimal:2): Consommation initiale
  - **initial_ges_emissions** (decimal:2): Émissions GES
  - **initial_energy_class** (string): Classe énergétique
  - **final_energy_consumption** (decimal:2): Consommation finale
  - **final_ges_emissions** (decimal:2): Émissions finales
  - **final_energy_class** (string): Nouvelle classe
  - **energy_class_gain** (integer): Gain de classes
  - **audit_fees** (decimal:2): Frais d'audit
  - **other_eligible_fees** (decimal:2): Autres frais
- **Dérogation**:
  - **derogation_request** (boolean): Demande de dérogation

### 8. Demandes d'aides
- **Demandes** (via table `ream_mar_aides`):
  - **aid_type** (string): Type d'aide
  - **aid_name** (string): Nom
  - **organization** (string): Organisme
  - **eligible_amount** (decimal:2): Montant éligible
  - **requested_amount** (decimal:2): Montant demandé
  - **request_date** (date): Date de demande

### 9. Réalisation du projet de travaux
- **Suivi** (via table `ream_mar_travaux`):
  - **planned_start_date** (date): Date début prévue
  - **planned_end_date** (date): Date fin prévue
  - **actual_start_date** (date): Date début réelle
  - **actual_end_date** (date): Date fin réelle
  - **status** (string): Statut

### 10. Fin de prestation
- **Synthèse finale** (via table `ream_mar_audits`): Reprise des détails de l'audit énergétique
- **Visite finale** (via table `ream_mar_visites`):
  - **final_visit_date** (date): Date
  - **final_visit_time** (datetime): Heure
  - **final_visit_duration** (integer): Durée
- **Facture de solde** (via table `ream_mar_factures`):
  - **invoice_number** (string): Numéro
  - **invoice_date** (date): Date
  - **amount_ht** (decimal:2): Montant HT
  - **amount_ttc** (decimal:2): Montant TTC
- **Mandataire** (via table `ream_mar_mandataires`):
  - **mandataire_iban** (string): IBAN
  - **mandataire_bic** (string): BIC
- **Visites supplémentaires**:
  - **intermediate_visits** (array): Visites intermédiaires
  - **complementary_visits** (array): Visites complémentaires

---

## Partie 3 - Gestion documentaire
- **Système de dépôt** (via table `ream_mar_documents`):
  - Formats supportés : PDF, DOC, JPG, PNG
  - Glisser-déposer via Filament relation manager
- **Champs**:
  - **document_type** (string): Type de document
  - **document_name** (string): Nom
  - **file_path** (string): Chemin
  - **file_size** (integer): Taille
  - **extension** (string): Extension
  - **upload_date** (datetime): Date d'upload

---

## Partie 4 - Historique des actions
- **Suivi** (via table `ream_mar_actions`):
  - **action_type** (string): Type d'action
  - **title** (string): Titre
  - **description** (text): Description
  - **action_date** (datetime): Date
  - **assignee_id** (integer): Utilisateur assigné
  - **due_date** (datetime): Date d'échéance
- Gestion via Filament relation manager

---

## Partie 5 - Signature
- **Suivi des signatures** (via table `ream_mar_signatures`):
  - **document_id** (integer): ID du document
  - **signatory_name** (string): Nom du signataire
  - **signatory_email** (string): Email
  - **signature_status** (string): Statut
  - **request_date** (datetime): Date de demande
  - **signature_date** (datetime): Date de signature
- Gestion via Filament relation manager

---

## Partie 6 - Mes Missions Audit
- **Gestion des audits** (Table: ream_mar_audit_missions):
  - **audit_mission_id** (integer): ID unique
  - **client_id** (integer): ID du client
  - **bien_id** (integer): ID du bien
  - **audit_status** (string): Statut
  - **audit_date** (date): Date
  - **created_by** (integer): ID de l'utilisateur ayant créé
  - **updated_by** (integer): ID de l'utilisateur ayant mis à jour
- **Fonctionnalité**:
  - Conversion des données d'audit en mission MAR
  - Gestion des étapes clés de l'audit

**Casts**:
```php
protected $casts = [
    'audit_date' => 'date',
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
```

---

## Partie 7 - Fonctionnalités CRM de base
Les fonctionnalités CRM sont ajoutées après les sections de gestion des missions et des clients, pour répondre aux besoins de gestion des prospects et des relations clients, tout en respectant la priorité donnée au métier principal du cabinet d'audit.

### 1. Gestion des leads (Table: ream_mar_leads)
- **lead_source** (string): Source du lead (site web, téléphone, email, etc.)
- **civility** (string): Civilité
- **first_name** (string): Prénom
- **last_name** (string): Nom
- **email** (string): Email
- **phone** (string): Téléphone
- **address** (string): Adresse
- **postal_code** (string): Code postal
- **city** (string): Ville
- **country** (string): Pays
- **interest_level** (string): Niveau d'intérêt (faible, moyen, élevé)
- **lead_status** (string): Statut (nouveau, contacté, qualifié, perdu)
- **notes** (text): Notes
- **created_by** (integer): ID de l'utilisateur ayant créé
- **updated_by** (integer): ID de l'utilisateur ayant mis à jour

**Casts**:
```php
protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
```

**Fonctionnalités**:
- Conversion d'un lead en client (table `ream_mar_clients`) avec transfert des informations.
- Filtrage des leads par statut, source ou niveau d'intérêt.
- Tableau de bord CRM avec aperçu des leads (nouveau, en cours, converti).

### 2. Gestion des interactions (Table: ream_mar_interactions)
- **lead_id** (integer): ID du lead
- **client_id** (integer): ID du client (si converti)
- **interaction_type** (string): Type (appel, email, réunion, etc.)
- **interaction_date** (datetime): Date
- **description** (text): Description
- **outcome** (string): Résultat (positif, négatif, en attente)
- **follow_up_date** (datetime): Date de suivi
- **assignee_id** (integer): Utilisateur assigné
- **created_by** (integer): ID de l'utilisateur ayant créé
- **updated_by** (integer): ID de l'utilisateur ayant mis à jour

**Casts**:
```php
protected $casts = [
    'interaction_date' => 'datetime',
    'follow_up_date' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
```

**Fonctionnalités**:
- Enregistrement des interactions avec les leads ou clients.
- Planification de rappels pour les suivis via `follow_up_date`.
- Historique des interactions lié à chaque lead/client.

### 3. Gestion des opportunités (Table: ream_mar_opportunities)
- **lead_id** (integer): ID du lead
- **client_id** (integer): ID du client (si converti)
- **opportunity_name** (string): Nom de l'opportunité
- **estimated_value** (decimal:2): Valeur estimée
- **probability** (integer): Probabilité de conversion (0-100%)
- **stage** (string): Étape (prospection, proposition, négociation, gagné, perdu)
- **expected_close_date** (date): Date de clôture prévue
- **description** (text): Description
- **created_by** (integer): ID de l'utilisateur ayant créé
- **updated_by** (integer): ID de l'utilisateur ayant mis à jour

**Casts**:
```php
protected $casts = [
    'estimated_value' => 'decimal:2',
    'probability' => 'integer',
    'expected_close_date' => 'date',
    'created_at' => 'datetime',
    'updated_at' => 'datetime'
];
```

**Fonctionnalités**:
- Suivi des opportunités liées à un lead ou client.
- Conversion d'une opportunité gagnée en mission MAR.
- Tableau de bord avec pipeline des opportunités par étape.

### 4. Automatisation et notifications
- **Notifications**:
  - Alertes pour les suivis de leads (basées sur `follow_up_date`).
  - Rappels pour les opportunités proches de la date de clôture.
- **Automatisation**:
  - Envoi d'emails prédéfinis pour les leads (ex. email de bienvenue après inscription).
  - Mise à jour automatique du statut des leads (ex. passage à "contacté" après une interaction).

---

## Relations entre modèles

### Relations existantes (inchangées)
- **Mission → Client** (belongsTo)
- **Mission → Bien** (belongsTo)
- **Mission → Visites** (hasMany)
- **Mission → Audit** (hasOne)
- **Mission → Travaux** (hasMany)
- **Mission → Financement** (hasOne)
- **Mission → Aides** (hasMany)
- **Mission → Documents** (hasMany)
- **Mission → Actions** (hasMany)
- **Mission → Signatures** (hasMany)
- **Mission → Evaluation** (hasOne)
- **Mission → Mandataire** (hasOne)
- **Mission → Factures** (hasMany)
- **Mission → Accompagnement** (hasOne)
- **Document → Signatures** (hasMany)
- **Action → User** (belongsTo pour `assignee_id`)
- **Signature → Document** (belongsTo)
- **Travaux → Documents** (hasMany pour devis)
- **Audit → Documents** (hasMany pour rapports)

### Nouvelles relations pour le CRM
- **Lead → Interactions** (hasMany)
- **Lead → Opportunities** (hasMany)
- **Client → Interactions** (hasMany)
- **Client → Opportunities** (hasMany)
- **Opportunity → Mission** (hasOne, pour conversion en mission MAR)
- **Interaction → User** (belongsTo pour `assignee_id`)

---

## Priorités du cabinet d'audit
- Le système met l'accent sur les missions MAR et les audits énergétiques, qui constituent le cœur de métier.
- Les fonctionnalités CRM sont secondaires et servent à optimiser la gestion des prospects et clients sans détourner l'attention des activités principales.
- L'interface Filament garantit une navigation fluide entre les sections (missions, audits, CRM).

---

## Fonctionnalités additionnelles
- **Reporting**:
  - Rapports sur les leads (nombre, statut, source).
  - Rapports sur les opportunités (pipeline, valeur totale, taux de conversion).
  - Export Excel pour le rapport annuel d'activité (inchangé).
- **Intégrations**:
  - Connexion avec calculateurs externes pour estimer les aides financières.
  - Intégration avec un service d'envoi d'emails pour les notifications CRM.
- **Sécurité**:
  - Gestion des rôles et permissions via Filament pour limiter l'accès aux données sensibles (ex. informations financières, documents).
- **Scalabilité**:
  - La base de données est conçue pour supporter un grand volume de leads, clients et missions.
  - Indexation des colonnes fréquemment utilisées pour les recherches (ex. `email`, `lead_status`).


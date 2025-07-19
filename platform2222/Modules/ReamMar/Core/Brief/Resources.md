Voici comment structurer cette approche
Cette approche présente plusieurs avantages :

## 🎯 **Avantages de cette structure**

### 1. **Parcours unifié**
- Une seule ressource `MissionMarResource` qui gère toutes les étapes
- Navigation fluide entre les étapes via les onglets
- Sauvegarde distribuée dans les bons modèles

### 2. **Gestion multi-modèles**
- Chaque page du parcours peut écrire dans plusieurs tables
- Trait `HandlesMultiModelSaving` pour réutiliser la logique
- Méthodes `mutateFormDataBeforeFill` et `mutateFormDataBeforeSave` pour orchestrer

### 3. **Expérience utilisateur optimisée**
- Navigation par onglets pour chaque mission
- Boutons "Précédent/Suivant" pour guider l'utilisateur
- Sauvegarde automatique à chaque étape

### 4. **Séparation des responsabilités**
- Missions MAR (parcours principal)
- Missions Audit (workflow spécifique)
- CRM (gestion commerciale)
- Paramètres (configuration)

## 🔧 **Recommandations techniques**

1. **Validations transversales** : Utiliser des Form Requests Laravel pour valider les données avant sauvegarde
2. **Transactions** : Enrober les sauvegardes multi-modèles dans des transactions DB
3. **États du parcours** : Ajouter un champ `step_status` dans la table missions pour tracker l'avancement
4. **Permissions** : Utiliser les policies Filament pour restreindre l'accès aux étapes selon les rôles



<?php

// Structure des ressources principales avec gestion multi-modèles

namespace Numerimondes\Modules\ReamMar\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Pages\SubNavigationPosition;

/**
 * Ressource principale pour les missions MAR
 * Gère l'ensemble du parcours client à travers plusieurs modèles
 */
class MissionMarResource extends Resource
{
    protected static ?string $model = \Numerimondes\Modules\ReamMar\Core\Models\Mission::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Missions MAR';
    protected static ?int $navigationSort = 1;
    
    // Pages du parcours mission
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMissions::route('/'),
            'create' => Pages\CreateMission::route('/create'),
            'edit' => Pages\EditMission::route('/{record}/edit'),
            
            // Étapes du parcours mission
            'contract' => Pages\MissionContract::route('/{record}/contract'),
            'initial-visit' => Pages\InitialVisit::route('/{record}/initial-visit'),
            'preliminary-meeting' => Pages\PreliminaryMeeting::route('/{record}/preliminary-meeting'),
            'evaluation' => Pages\HousingEvaluation::route('/{record}/evaluation'),
            'audit' => Pages\EnergyAudit::route('/{record}/audit'),
            'financing' => Pages\FinancingPlan::route('/{record}/financing'),
            'work-project' => Pages\WorkProject::route('/{record}/work-project'),
            'aid-requests' => Pages\AidRequests::route('/{record}/aid-requests'),
            'work-execution' => Pages\WorkExecution::route('/{record}/work-execution'),
            'completion' => Pages\ServiceCompletion::route('/{record}/completion'),
            
            // Gestion transversale
            'documents' => Pages\MissionDocuments::route('/{record}/documents'),
            'actions' => Pages\MissionActions::route('/{record}/actions'),
            'signatures' => Pages\MissionSignatures::route('/{record}/signatures'),
        ];
    }
    
    // Navigation par onglets pour chaque mission
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            'edit' => 'Informations générales',
            'contract' => 'Contrat',
            'initial-visit' => 'Visite initiale',
            'preliminary-meeting' => 'RDV préalable',
            'evaluation' => 'Évaluation',
            'audit' => 'Audit énergétique',
            'financing' => 'Financement',
            'work-project' => 'Projet travaux',
            'aid-requests' => 'Demandes d\'aides',
            'work-execution' => 'Réalisation',
            'completion' => 'Fin de prestation',
            'documents' => 'Documents',
            'actions' => 'Actions',
            'signatures' => 'Signatures',
        ]);
    }
}

/**
 * Ressource pour les missions audit (séparée car workflow différent)
 */
class AuditMissionResource extends Resource
{
    protected static ?string $model = \Numerimondes\Modules\ReamMar\Core\Models\AuditMission::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Missions Audit';
    protected static ?int $navigationSort = 2;
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditMissions::route('/'),
            'create' => Pages\CreateAuditMission::route('/create'),
            'edit' => Pages\EditAuditMission::route('/{record}/edit'),
            'convert' => Pages\ConvertToMar::route('/{record}/convert-to-mar'),
        ];
    }
}

/**
 * Ressource CRM pour les leads et opportunités
 */
class CrmResource extends Resource
{
    protected static ?string $model = \Numerimondes\Modules\ReamMar\Core\Models\Lead::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'CRM';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Commercial';
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\CrmDashboard::route('/'),
            'leads' => Pages\ManageLeads::route('/leads'),
            'opportunities' => Pages\ManageOpportunities::route('/opportunities'),
            'interactions' => Pages\ManageInteractions::route('/interactions'),
            'clients' => Pages\ManageClients::route('/clients'),
        ];
    }
}

/**
 * Ressource pour les paramètres de l'entreprise
 */
class SettingsResource extends Resource
{
    protected static ?string $model = \Numerimondes\Modules\ReamMar\Core\Models\CompanySetting::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Paramètres';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'Configuration';
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\CompanySettings::route('/'),
            'contract-clauses' => Pages\ContractClauses::route('/contract-clauses'),
            'contract-templates' => Pages\ContractTemplates::route('/contract-templates'),
            'check-devis' => Pages\CheckDevis::route('/check-devis'),
            'annual-report' => Pages\AnnualReport::route('/annual-report'),
        ];
    }
}

// Exemple d'une page de parcours qui gère plusieurs modèles
namespace Numerimondes\Modules\ReamMar\Filament\Resources\MissionMarResource\Pages;

use Filament\Forms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Numerimondes\Modules\ReamMar\Core\Models\{Mission, Client, Bien, Financement, Audit};

class FinancingPlan extends EditRecord
{
    protected static string $resource = MissionMarResource::class;
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make('Financement')
                ->tabs([
                    // Onglet 1: Situation initiale (via model Audit)
                    Forms\Components\Tabs\Tab::make('Situation initiale')
                        ->schema([
                            Forms\Components\TextInput::make('audit.initial_energy_consumption')
                                ->label('Consommation énergétique initiale')
                                ->numeric()
                                ->suffix('kWh/m²/an'),
                            
                            Forms\Components\Select::make('audit.initial_energy_class')
                                ->label('Classe énergétique')
                                ->options([
                                    'A' => 'A',
                                    'B' => 'B',
                                    'C' => 'C',
                                    'D' => 'D',
                                    'E' => 'E',
                                    'F' => 'F',
                                    'G' => 'G',
                                ]),
                        ]),
                    
                    // Onglet 2: Travaux envisagés (via model Financement)
                    Forms\Components\Tabs\Tab::make('Travaux envisagés')
                        ->schema([
                            Forms\Components\TextInput::make('financement.estimated_work_amount_ht')
                                ->label('Montant estimé HT')
                                ->numeric()
                                ->prefix('€'),
                            
                            Forms\Components\TextInput::make('financement.estimated_work_amount_ttc')
                                ->label('Montant estimé TTC')
                                ->numeric()
                                ->prefix('€'),
                            
                            Forms\Components\TextInput::make('financement.support_fees')
                                ->label('Frais d\'accompagnement')
                                ->numeric()
                                ->prefix('€'),
                        ]),
                    
                    // Onglet 3: Aides financières
                    Forms\Components\Tabs\Tab::make('Aides financières')
                        ->schema([
                            Forms\Components\TextInput::make('financement.estimated_aids')
                                ->label('Aides estimées')
                                ->numeric()
                                ->prefix('€'),
                            
                            Forms\Components\TextInput::make('financement.remaining_charge')
                                ->label('Reste à charge')
                                ->numeric()
                                ->prefix('€'),
                        ]),
                    
                    // Onglet 4: Financement
                    Forms\Components\Tabs\Tab::make('Financement')
                        ->schema([
                            Forms\Components\Section::make('ÉcoPTZ')
                                ->schema([
                                    Forms\Components\TextInput::make('financement.ecoptz_amount')
                                        ->label('Montant ÉcoPTZ')
                                        ->numeric()
                                        ->prefix('€'),
                                    
                                    Forms\Components\TextInput::make('financement.ecoptz_rate')
                                        ->label('Taux')
                                        ->numeric()
                                        ->suffix('%'),
                                    
                                    Forms\Components\TextInput::make('financement.ecoptz_duration_months')
                                        ->label('Durée (mois)')
                                        ->numeric(),
                                    
                                    Forms\Components\TextInput::make('financement.ecoptz_monthly_payment')
                                        ->label('Mensualité')
                                        ->numeric()
                                        ->prefix('€'),
                                ]),
                            
                            Forms\Components\Section::make('Prêt bancaire')
                                ->schema([
                                    Forms\Components\TextInput::make('financement.bank_loan_amount')
                                        ->label('Montant prêt bancaire')
                                        ->numeric()
                                        ->prefix('€'),
                                    
                                    Forms\Components\TextInput::make('financement.bank_loan_rate')
                                        ->label('Taux')
                                        ->numeric()
                                        ->suffix('%'),
                                    
                                    Forms\Components\TextInput::make('financement.bank_loan_duration_months')
                                        ->label('Durée (mois)')
                                        ->numeric(),
                                    
                                    Forms\Components\TextInput::make('financement.bank_loan_monthly_payment')
                                        ->label('Mensualité')
                                        ->numeric()
                                        ->prefix('€'),
                                ]),
                        ]),
                    
                    // Onglet 5: Économies énergétiques
                    Forms\Components\Tabs\Tab::make('Économies énergétiques')
                        ->schema([
                            Forms\Components\TextInput::make('financement.energy_expenses_before')
                                ->label('Dépenses énergétiques avant travaux')
                                ->numeric()
                                ->prefix('€'),
                            
                            Forms\Components\TextInput::make('financement.energy_expenses_after')
                                ->label('Dépenses énergétiques après travaux')
                                ->numeric()
                                ->prefix('€'),
                            
                            Forms\Components\TextInput::make('financement.monthly_savings')
                                ->label('Économies mensuelles')
                                ->numeric()
                                ->prefix('€'),
                        ]),
                ])
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Charger les données des modèles liés
        $mission = $this->record;
        
        // Charger ou créer les relations
        $audit = $mission->audit ?? new Audit();
        $financement = $mission->financement ?? new Financement();
        
        // Fusionner les données
        $data['audit'] = $audit->toArray();
        $data['financement'] = $financement->toArray();
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Sauvegarder dans les modèles appropriés
        $mission = $this->record;
        
        // Sauvegarder l'audit
        if (isset($data['audit'])) {
            $audit = $mission->audit ?? new Audit();
            $audit->fill($data['audit']);
            $audit->mission_id = $mission->id;
            $audit->save();
            
            unset($data['audit']);
        }
        
        // Sauvegarder le financement
        if (isset($data['financement'])) {
            $financement = $mission->financement ?? new Financement();
            $financement->fill($data['financement']);
            $financement->mission_id = $mission->id;
            $financement->save();
            
            unset($data['financement']);
        }
        
        return $data;
    }
    
    protected function getActions(): array
    {
        return [
            Actions\Action::make('previous')
                ->label('Étape précédente')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => MissionMarResource::getUrl('audit', ['record' => $this->record])),
            
            Actions\Action::make('next')
                ->label('Étape suivante')
                ->icon('heroicon-o-arrow-right')
                ->url(fn () => MissionMarResource::getUrl('work-project', ['record' => $this->record])),
            
            Actions\Action::make('save')
                ->label('Sauvegarder')
                ->action('save'),
        ];
    }
}

/**
 * Trait pour gérer les sauvegardes multi-modèles
 */
trait HandlesMultiModelSaving
{
    protected function saveRelatedModels(array $data, string $relationName, string $modelClass): void
    {
        if (isset($data[$relationName])) {
            $relation = $this->record->{$relationName} ?? new $modelClass();
            $relation->fill($data[$relationName]);
            $relation->mission_id = $this->record->id;
            $relation->save();
        }
    }
    
    protected function loadRelatedModels(array $data, array $relations): array
    {
        foreach ($relations as $relationName => $modelClass) {
            $relation = $this->record->{$relationName} ?? new $modelClass();
            $data[$relationName] = $relation->toArray();
        }
        
        return $data;
    }
}

// Exemple d'utilisation du trait
class WorkProject extends EditRecord
{
    use HandlesMultiModelSaving;
    
    protected static string $resource = MissionMarResource::class;
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Sauvegarder dans plusieurs modèles
        $this->saveRelatedModels($data, 'audit', Audit::class);
        $this->saveRelatedModels($data, 'financement', Financement::class);
        
        // Sauvegarder les travaux (hasMany)
        if (isset($data['travaux'])) {
            foreach ($data['travaux'] as $travauxData) {
                $travaux = new \Numerimondes\Modules\ReamMar\Core\Models\Travaux();
                $travaux->fill($travauxData);
                $travaux->mission_id = $this->record->id;
                $travaux->save();
            }
            unset($data['travaux']);
        }
        
        return $data;
    }
}



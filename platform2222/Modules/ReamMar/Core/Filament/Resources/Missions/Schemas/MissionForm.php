<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Missions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Webkernel\CRM\Models\Pipeline;
use Webkernel\CRM\Models\PipelineStage;

class MissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // PARTIE 1 - Informations principales avec références
                Section::make('Références Générales')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // COL 1 - Prospect
                                Grid::make(1)
                                    ->schema([
                                        Select::make('prospect_type')
                                            ->label('Type de prospect')
                                            ->options([
                                                'website' => 'Site web',
                                                'phone' => 'Téléphone',
                                                'email' => 'Email',
                                                'referral' => 'Recommandation',
                                                'social_media' => 'Réseaux sociaux',
                                                'other' => 'Autre'
                                            ])
                                            ->required(),
                                        Select::make('prospect_source')
                                            ->label('Source')
                                            ->options([
                                                'google' => 'Google',
                                                'facebook' => 'Facebook',
                                                'instagram' => 'Instagram',
                                                'linkedin' => 'LinkedIn',
                                                'direct' => 'Contact direct',
                                                'partner' => 'Partenaires',
                                                'other' => 'Autre'
                                            ])
                                            ->required(),
                                    ])
                                    ->columnSpan(1),

                                // COL 2 - Contact
                                Grid::make(1)
                                    ->schema([
                                        DatePicker::make('contact_date')
                                            ->label('Date de contact')
                                            ->required(),
                                        Select::make('contact_method')
                                            ->label('Méthode de contact')
                                            ->options([
                                                'phone' => 'Téléphone',
                                                'email' => 'Email',
                                                'meeting' => 'Rendez-vous',
                                                'video_call' => 'Visioconférence',
                                                'other' => 'Autre'
                                            ])
                                            ->required(),
                                    ])
                                    ->columnSpan(1),

                                // COL 3 - Opportunité
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('opportunity_value')
                                            ->label('Valeur estimée (€)')
                                            ->numeric()
                                            ->prefix('€'),
                                        Select::make('opportunity_probability')
                                            ->label('Probabilité (%)')
                                            ->options([
                                                10 => '10% - Très faible',
                                                25 => '25% - Faible',
                                                50 => '50% - Moyenne',
                                                75 => '75% - Élevée',
                                                90 => '90% - Très élevée',
                                                100 => '100% - Gagnée'
                                            ])
                                            ->required(),
                                    ])
                                    ->columnSpan(1),
                            ]),
                        Select::make('opportunity_stage')
                            ->label('Étape')
                            ->options([
                                'prospecting' => 'Prospection',
                                'qualification' => 'Qualification',
                                'proposal' => 'Proposition',
                                'negotiation' => 'Négociation',
                                'won' => 'Gagnée',
                                'lost' => 'Perdue'
                            ])
                            ->required(),
                        Grid::make(3)
                            ->schema([
                                Textarea::make('prospect_notes')
                                    ->label('Notes prospect')
                                    ->rows(3),
                                Textarea::make('contact_notes')
                                    ->label('Notes du contact')
                                    ->rows(3),
                                Textarea::make('opportunity_notes')
                                    ->label('Notes opportunité')
                                    ->rows(3),
                            ]),
                    ]),

                // PARTIE 2 - CRM Pipeline
                Section::make('Pipeline CRM')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('pipeline_id')
                                    ->label('Pipeline')
                                    ->options(Pipeline::where('module', 'ream_mar')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->reactive(),
                                Select::make('pipeline_stage_id')
                                    ->label('Étape du pipeline')
                                    ->options(function ($get) {
                                        $pipelineId = $get('pipeline_id');
                                        if (!$pipelineId) return [];
                                        
                                        return PipelineStage::where('pipeline_id', $pipelineId)
                                            ->orderBy('order')
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('deal_status')
                                    ->label('Statut du deal')
                                    ->options([
                                        'active' => 'Actif',
                                        'won' => 'Gagné',
                                        'lost' => 'Perdu',
                                        'cancelled' => 'Annulé'
                                    ])
                                    ->default('active'),
                                TextInput::make('deal_value')
                                    ->label('Valeur du deal (€)')
                                    ->numeric()
                                    ->prefix('€'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('expected_close_date')
                                    ->label('Date de clôture prévue'),
                                DatePicker::make('actual_close_date')
                                    ->label('Date de clôture réelle'),
                            ]),
                    ]),

                // PARTIE 3 - Onglets d'informations
                Tabs::make('Informations')
                    ->tabs([
                        Tab::make('Client')
                            ->schema([
                                Select::make('client_id')
                                    ->label('Client')
                                    ->relationship('client', 'first_name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record?->full_name ?? 'Client inconnu')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('civility')
                                                    ->label('Civilité')
                                                    ->options([
                                                        'M.' => 'M.',
                                                        'Mme' => 'Mme',
                                                        'Mlle' => 'Mlle',
                                                        'Dr' => 'Dr',
                                                        'Pr' => 'Pr'
                                                    ])
                                                    ->required(),
                                                TextInput::make('first_name')
                                                    ->label('Prénom')
                                                    ->required(),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('last_name')
                                                    ->label('Nom')
                                                    ->required(),
                                                TextInput::make('email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->required(),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('phone')
                                                    ->label('Téléphone')
                                                    ->tel(),
                                                TextInput::make('mobile')
                                                    ->label('Mobile')
                                                    ->tel(),
                                            ]),
                                        FileUpload::make('avatar')
                                            ->label('Avatar')
                                            ->image()
                                            ->imageEditor()
                                            ->circleCropper(),
                                        Textarea::make('address')
                                            ->label('Adresse')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('postal_code')
                                                    ->label('Code postal'),
                                                TextInput::make('city')
                                                    ->label('Ville'),
                                                TextInput::make('country')
                                                    ->label('Pays')
                                                    ->default('France'),
                                            ]),
                                        Textarea::make('notes')
                                            ->label('Notes client')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->required()
                                    ->afterStateUpdated(function ($state, $livewire) {
                                        $livewire->dispatch('refresh-sidebar');
                                    }),
                            ]),

                        Tab::make('Propriété')
                            ->schema([
                                Select::make('bien_id')
                                    ->label('Propriété')
                                    ->relationship('bien', 'address')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record?->full_address ?? 'Propriété inconnue')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Textarea::make('address')
                                            ->label('Adresse du bien')
                                            ->rows(2)
                                            ->required()
                    ->columnSpanFull(),
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('postal_code')
                                                    ->label('Code postal')
                                                    ->required(),
                                                TextInput::make('city')
                                                    ->label('Ville')
                                                    ->required(),
                                                TextInput::make('country')
                                                    ->label('Pays')
                                                    ->default('France'),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('type')
                                                    ->label('Type de bien')
                                                    ->options([
                                                        'house' => 'Maison',
                                                        'apartment' => 'Appartement',
                                                        'villa' => 'Villa',
                                                        'duplex' => 'Duplex',
                                                        'loft' => 'Loft',
                                                        'other' => 'Autre'
                                                    ])
                                                    ->required(),
                                                Select::make('usage')
                                                    ->label('Usage')
                                                    ->options([
                                                        'primary_residence' => 'Résidence principale',
                                                        'secondary_residence' => 'Résidence secondaire',
                                                        'rental' => 'Location',
                                                        'investment' => 'Investissement',
                                                        'other' => 'Autre'
                                                    ])
                                                    ->required(),
                                            ]),
                                        Select::make('household_status')
                                            ->label('Statut du ménage')
                                            ->options([
                                                'owner_occupant' => 'Propriétaire occupant',
                                                'landlord' => 'Bailleur',
                                                'tenant' => 'Locataire',
                                                'co_owner' => 'Copropriétaire',
                                                'other' => 'Autre'
                                            ])
                                            ->required(),
                                        Textarea::make('notes')
                                            ->label('Notes propriété')
                                            ->rows(3)
                    ->columnSpanFull(),
                                    ])
                                    ->required(),
                            ]),

                        Tab::make('Audit')
                            ->schema([
                                Select::make('audit_id')
                                    ->label('Audit')
                                    ->relationship('audit', 'type')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record?->type_label ?? 'Audit inconnu')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->schema([
                                                DatePicker::make('audit_date')
                                                    ->label('Date d\'audit')
                                                    ->required(),
                Select::make('audit_type')
                                                    ->label('Type d\'audit')
                                                    ->options([
                                                        'energy' => 'Énergétique',
                                                        'thermal' => 'Thermique',
                                                        'complete' => 'Complet',
                                                        'diagnostic' => 'Diagnostic'
                                                    ])
                                                    ->required(),
                                            ]),
                                        TextInput::make('audit_fees')
                                            ->label('Frais d\'audit (€)')
                                            ->numeric()
                                            ->prefix('€'),
                                        FileUpload::make('audit_report_path')
                                            ->label('Rapport d\'audit')
                                            ->acceptedFileTypes(['application/pdf']),
                                        Textarea::make('audit_notes')
                                            ->label('Notes audit')
                                            ->rows(3)
                    ->columnSpanFull(),
                                        Toggle::make('is_required')
                                            ->label('Audit requis')
                                            ->default(false),
                                    ]),
                            ]),
                    ]),

                // PARTIE 4 - Onglets de financement et travaux
                Tabs::make('Financement et Travaux')
                    ->tabs([
                        Tab::make('Financement')
                            ->schema([
                                Select::make('financement_id')
                                    ->label('Financement')
                                    ->relationship('financement', 'amount')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record ? '€' . number_format($record->amount, 2) : 'Financement inconnu')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('financing_amount')
                                                    ->label('Montant total (€)')
                                                    ->numeric()
                                                    ->prefix('€')
                                                    ->required(),
                                                TextInput::make('financing_aids')
                                                    ->label('Aides financières (€)')
                                                    ->numeric()
                                                    ->prefix('€'),
                                            ]),
                                        TextInput::make('financing_loan')
                                            ->label('Prêt bancaire (€)')
                                            ->numeric()
                                            ->prefix('€'),
                                        Section::make('ÉcoPTZ')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextInput::make('ecoptz_amount')
                                                            ->label('Montant ÉcoPTZ (€)')
                    ->numeric()
                                                            ->prefix('€'),
                                                        TextInput::make('ecoptz_rate')
                                                            ->label('Taux (%)')
                    ->numeric()
                                                            ->suffix('%'),
                                                        TextInput::make('ecoptz_duration_months')
                                                            ->label('Durée (mois)')
                                                            ->numeric(),
                                                    ]),
                                            ])
                                            ->collapsible(),
                                        Section::make('Prêt Bancaire')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        TextInput::make('bank_loan_amount')
                                                            ->label('Montant prêt (€)')
                    ->numeric()
                                                            ->prefix('€'),
                                                        TextInput::make('bank_loan_rate')
                                                            ->label('Taux (%)')
                    ->numeric()
                                                            ->suffix('%'),
                                                        TextInput::make('bank_loan_duration_months')
                                                            ->label('Durée (mois)')
                                                            ->numeric(),
                                                    ]),
                                            ])
                                            ->collapsible(),
                Textarea::make('financing_notes')
                                            ->label('Notes financement')
                                            ->rows(3)
                    ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Travaux')
                            ->schema([
                                Select::make('travaux_id')
                                    ->label('Travaux')
                                    ->relationship('travaux', 'description')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record ? $record->description . ' - €' . number_format($record->amount, 2) : 'Travaux inconnus')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->schema([
                                                DatePicker::make('work_start_date')
                                                    ->label('Date début prévue'),
                                                DatePicker::make('work_end_date')
                                                    ->label('Date fin prévue'),
                                            ]),
                Select::make('work_status')
                                            ->label('Statut travaux')
                    ->options([
                                                'planned' => 'Planifié',
                                                'in_progress' => 'En cours',
                                                'completed' => 'Terminé',
                                                'on_hold' => 'En attente',
                                                'cancelled' => 'Annulé'
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('work_amount')
                                                    ->label('Montant travaux (€)')
                                                    ->numeric()
                                                    ->prefix('€'),
                                                TextInput::make('work_company')
                                                    ->label('Entreprise'),
                                            ]),
                                        TextInput::make('work_company_siret')
                                            ->label('SIRET entreprise'),
                Textarea::make('work_description')
                                            ->label('Description travaux')
                                            ->rows(4)
                    ->columnSpanFull(),
                                        Textarea::make('work_notes')
                                            ->label('Notes travaux')
                                            ->rows(3)
                    ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),

                // PARTIE 5 - Workflow et finalisation
                Section::make('Workflow et Finalisation')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('current_step')
                                    ->label('Étape actuelle')
                                    ->options([
                                        'prospect' => 'Prospect',
                                        'contact' => 'Contact',
                                        'opportunity' => 'Opportunité',
                                        'client' => 'Client',
                                        'bien' => 'Propriété',
                                        'audit' => 'Audit',
                                        'financing' => 'Financement',
                                        'work' => 'Travaux',
                                        'completion' => 'Finalisation'
                                    ])
                    ->required(),
                                Select::make('step_status')
                                    ->label('Statut étape')
                                    ->options([
                                        'pending' => 'En attente',
                                        'in_progress' => 'En cours',
                                        'completed' => 'Terminé',
                                        'blocked' => 'Bloqué'
                                    ])
                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('completion_date')
                                    ->label('Date de finalisation'),
                                DatePicker::make('validation_date')
                                    ->label('Date de validation'),
                            ]),
                        Select::make('completion_status')
                            ->label('Statut final')
                            ->options([
                                'completed' => 'Terminé avec succès',
                                'partially_completed' => 'Partiellement terminé',
                                'cancelled' => 'Annulé',
                                'on_hold' => 'En attente'
                            ]),
                        Grid::make(2)
                            ->schema([
                                Textarea::make('completion_notes')
                                    ->label('Notes de finalisation')
                                    ->rows(4),
                                Textarea::make('workflow_notes')
                                    ->label('Notes workflow')
                                    ->rows(4),
                            ]),
                    ]),
            ]);
    }
}

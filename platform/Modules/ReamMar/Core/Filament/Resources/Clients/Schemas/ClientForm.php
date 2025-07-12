<?php
namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Schemas;

use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Numerimondes\Modules\ReamMar\Core\Models\Client;

class ClientForm
{
    // Barèmes ANAH 2024 pour le calcul automatique du type de foyer
    private static array $revenusBaremes = [
        'very_modest' => [
            1 => 22461, 2 => 32967, 3 => 39691, 4 => 46426, 5 => 53152,
            6 => 59894, 7 => 66625, 8 => 73357
        ],
        'modest' => [
            1 => 27343, 2 => 40130, 3 => 48197, 4 => 56277, 5 => 64380,
            6 => 72479, 7 => 80578, 8 => 88679
        ]
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make()
                    ->steps([
                        self::getPersonalInformationStep(),
                        self::getFiscalAddressStep(),
                        self::getProjectAddressStep(),
                        self::getHouseholdCompositionStep(),
                        self::getContractDetailsStep(),
                        self::getMandatesAndAgentsStep(),
                        self::getDocumentsStep(),
                    ])
                    ->skippable(false) // Navigation séquentielle obligatoire
                    ->columnSpanFull()
                    ->persistStepInQueryString() // Permet de revenir à une étape via URL
                    ->submitAction(
                        Action::make('create')
                            ->label('Créer le dossier client')
                            ->icon('heroicon-o-check-circle')
                            ->submit('create')
                    )
                    ->extraAttributes([
                        'class' => 'fi-wizard-vertical-compact',
                        'style' => 'max-width: 100%; --wizard-step-spacing: 0.75rem;'
                    ]),
            ])
            ->extraAttributes([
                'class' => 'client-form-wrapper'
            ]);
    }

    public static function getPersonalInformationStep(): Step
    {
        return Step::make('Informations personnelles')
            ->description('Identité du client')
            ->icon('heroicon-o-user')
            ->schema([
                Group::make([
                    self::getCivilityField(),
                    self::getFirstNameField(),
                    self::getLastNameField(),
                ])
                ->columns(3)
                ->columnSpanFull(),
                Group::make([
                    self::getFolderNameField()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            if (empty($state)) {
                                $firstName = $get('first_name');
                                $lastName = $get('last_name');
                                if ($firstName && $lastName) {
                                    $set('folder_name', Str::upper($lastName) . '_' . Str::upper($firstName) . '_' . date('Y'));
                                }
                            }
                        }),
                ])
                ->columnSpanFull(),
                Section::make('Contact principal')
                    ->schema([
                        Group::make([
                            self::getEmailField(),
                            self::getPhonesField(),
                        ])
                        ->columns(2),

                        Group::make([
                            self::getPasswordField(),
                            self::getCanLoginField(),
                        ])
                        ->columns(2),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ])
            ->columns(1);
    }

    public static function getFiscalAddressStep(): Step
    {
        return Step::make('Adresse fiscale')
            ->description('Domicile fiscal du client')
            ->icon('heroicon-o-home')
            ->schema([
                Section::make('Adresse du domicile fiscal')
                    ->description('Cette adresse sera utilisée pour la facturation et les documents officiels')
                    ->schema([
                        self::getFiscalAddressField(),

                        Group::make([
                            self::getFiscalPostalCodeField(),
                            self::getFiscalCityField(),
                        ])
                        ->columns(2),

                        self::getFiscalCountryField(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getProjectAddressStep(): Step
    {
        return Step::make('Adresse du projet')
            ->description('Lieu des travaux d\'audit énergétique')
            ->icon('heroicon-o-building-office')
            ->schema([
                Toggle::make('same_as_fiscal_address')
                    ->label('L\'adresse du projet est identique à l\'adresse fiscale')
                    ->default(true)
                    ->live()
                    ->columnSpanFull(),
                Section::make('Adresse du projet')
                    ->schema([
                        TextInput::make('project_street')
                            ->label('Adresse')
                            ->required()
                            ->maxLength(255),

                        Group::make([
                            TextInput::make('project_postal_code')
                                ->label('Code postal')
                                ->required()
                                ->maxLength(10),

                            TextInput::make('project_city')
                                ->label('Ville')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(2),

                        TextInput::make('project_country')
                            ->label('Pays')
                            ->default('France')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->hidden(fn (Get $get) => $get('same_as_fiscal_address'))
                    ->columnSpanFull(),
            ]);
    }

    public static function getHouseholdCompositionStep(): Step
    {
        return Step::make('Composition du foyer')
            ->description('Revenus et calcul automatique du type de foyer')
            ->icon('heroicon-o-users')
            ->schema([
                Section::make('Composition du foyer fiscal')
                    ->schema([
                        Group::make([
                            TextInput::make('household_composition')
                                ->label('Nombre de personnes dans le foyer')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(12)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    self::calculateHouseholdCategory($state, $get('reference_tax_income'), $set);
                                }),

                            TextInput::make('reference_tax_income')
                                ->label('Revenu fiscal de référence (€)')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    self::calculateHouseholdCategory($get('household_composition'), $state, $set);
                                }),
                        ])
                        ->columns(2),
                        Placeholder::make('household_category_info')
                            ->label('Catégorie de foyer calculée automatiquement')
                            ->content(function (Get $get) {
                                $category = $get('household_category');
                                return match($category) {
                                    'very_modest' => ' Ménages très modestes - Éligible aux aides maximales',
                                    'modest' => ' Ménages modestes - Éligible aux aides intermédiaires',
                                    'intermediate' => ' Ménages intermédiaires - Éligible aux aides réduites',
                                    'superior' => ' Ménages supérieurs - Non éligible aux aides ANAH',
                                    default => ' Saisissez les informations pour calculer la catégorie'
                                };
                            })
                            ->columnSpanFull(),
                        Hidden::make('household_category'),
                        Hidden::make('household_status'), // Sera mappé depuis household_category
                    ])
                    ->columnSpanFull(),
                Section::make('Type d\'usage du logement')
                    ->schema([
                        self::getUsageTypeField(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getContractDetailsStep(): Step
    {
        return Step::make('Détails du contrat')
            ->description('Informations contractuelles et mission')
            ->icon('heroicon-o-document-text')
            ->schema([
                Section::make('Informations du projet')
                    ->schema([
                        TextInput::make('project_name')
                            ->label('Nom du projet')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Audit énergétique - Maison individuelle'),

                        DatePicker::make('contract_date')
                            ->label('Date du contrat')
                            ->required()
                            ->default(today()),
                    ])
                    ->columns(2),
                Section::make('Montants de la mission')
                    ->schema([
                        Group::make([
                            TextInput::make('mission_amount_excl_tax')
                                ->label('Montant HT (€)')
                                ->numeric()
                                ->required()
                                ->default(1666.67)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $vat = $state * 0.20;
                                    $ttc = $state + $vat;
                                    $set('vat', round($vat, 2));
                                    $set('mission_amount_incl_tax', round($ttc, 2));
                                }),

                            TextInput::make('vat')
                                ->label('TVA (€)')
                                ->numeric()
                                ->disabled()
                                ->default(333.33),

                            TextInput::make('mission_amount_incl_tax')
                                ->label('Montant TTC (€)')
                                ->numeric()
                                ->disabled()
                                ->default(2000.00),
                        ])
                        ->columns(3),

                        TextInput::make('first_installment')
                            ->label('Premier acompte (€)')
                            ->numeric()
                            ->nullable()
                            ->placeholder('Optionnel'),
                    ])
                    ->columnSpanFull(),
                Section::make('Informations de l\'entreprise')
                    ->schema([
                        Group::make([
                            TextInput::make('company_name')
                                ->label('Nom de l\'entreprise')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('siret_number')
                                ->label('Numéro SIRET')
                                ->required()
                                ->maxLength(14)
                                ->minLength(14),
                        ])
                        ->columns(2),

                        TextInput::make('mar_approval_number')
                            ->label('Numéro d\'agrément MAR')
                            ->required()
                            ->maxLength(255),

                        Group::make([
                            Toggle::make('mar_administrative_agent')
                                ->label('Agent administratif MAR'),

                            Toggle::make('mar_financial_agent')
                                ->label('Agent financier MAR'),
                        ])
                        ->columns(2),
                    ])
                    ->columnSpanFull(),
                Section::make('Adresse du siège social')
                    ->schema([
                        TextInput::make('head_office_address')
                            ->label('Adresse')
                            ->required()
                            ->maxLength(255),

                        Group::make([
                            TextInput::make('head_office_postal_code')
                                ->label('Code postal')
                                ->required()
                                ->maxLength(10),

                            TextInput::make('head_office_city')
                                ->label('Ville')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(2),

                        Group::make([
                            TextInput::make('company_phone')
                                ->label('Téléphone entreprise')
                                ->tel()
                                ->required()
                                ->maxLength(255),

                            TextInput::make('company_email')
                                ->label('Email entreprise')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(2),
                    ])
                    ->columnSpanFull(),
                Section::make('Assurance')
                    ->schema([
                        Group::make([
                            TextInput::make('insurer')
                                ->label('Assureur')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('insurance_policy_number')
                                ->label('Numéro de police d\'assurance')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->columns(2),
                    ])
                    ->columnSpanFull(),
                Section::make('Signature électronique')
                    ->schema([
                        Select::make('signature_provider')
                            ->label('Fournisseur de signature')
                            ->options([
                                'docusign' => 'DocuSign',
                                'docapost' => 'Docapost',
                                'yousign' => 'YouSign',
                                'handwritten_signature' => 'Signature manuscrite',
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('signature_provider_id')
                            ->label('ID du fournisseur')
                            ->maxLength(255)
                            ->hidden(fn (Get $get) => $get('signature_provider') === 'handwritten_signature'),

                        Group::make([
                            Textarea::make('mar_signature_link')
                                ->label('Lien signature MAR')
                                ->maxLength(1000)
                                ->rows(2),

                            Textarea::make('client_signature_link')
                                ->label('Lien signature client')
                                ->maxLength(1000)
                                ->rows(2),
                        ])
                        ->columns(2)
                        ->hidden(fn (Get $get) => $get('signature_provider') === 'handwritten_signature'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getMandatesAndAgentsStep(): Step
    {
        return Step::make('Mandats et agents')
            ->description('Gestion des intervenants externes')
            ->icon('heroicon-o-user-group')
            ->schema([
                Section::make('Type de formulaire CERFA')
                    ->schema([
                        Radio::make('cerfa_type')
                            ->label('Type de CERFA')
                            ->options([
                                '15923_01' => 'CERFA 15923*01 - Demande d\'aide pour la rénovation énergétique',
                                '16089_02' => 'CERFA 16089*02 - Demande de prime à l\'amélioration de l\'habitat',
                            ])
                            ->descriptions([
                                '15923_01' => 'Pour les travaux de rénovation énergétique globale',
                                '16089_02' => 'Pour les travaux d\'amélioration de l\'habitat spécifiques',
                            ])
                            ->nullable(),
                    ])
                    ->columnSpanFull(),
                Section::make('Agents externes')
                    ->description('Ajoutez les agents externes qui interviendront sur ce dossier')
                    ->schema([
                        Repeater::make('external_agents')
                            ->label('Agents externes')
                            ->schema([
                                Group::make([
                                    TextInput::make('company_name')
                                        ->label('Nom de l\'entreprise')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('siret_number')
                                        ->label('Numéro SIRET')
                                        ->required()
                                        ->maxLength(14),
                                ])
                                ->columns(2),

                                Group::make([
                                    TextInput::make('agent_email')
                                        ->label('Email agent')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('registration_number')
                                        ->label('Numéro d\'enregistrement')
                                        ->maxLength(255),
                                ])
                                ->columns(2),

                                Group::make([
                                    Toggle::make('administrative_agent')
                                        ->label('Agent administratif'),

                                    Toggle::make('financial_agent')
                                        ->label('Agent financier'),
                                ])
                                ->columns(2),

                                FileUpload::make('mandate_document')
                                    ->label('Document de mandat')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(5120) // 5MB
                                    ->nullable(),
                            ])
                            ->addActionLabel('Ajouter un agent externe')
                            ->collapsible()
                            ->cloneable()
                            ->deleteAction(
                                fn (Action $action) => $action->requiresConfirmation()
                            )
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getDocumentsStep(): Step
    {
        return Step::make('Documents')
            ->description('Upload des pièces justificatives')
            ->icon('heroicon-o-document-duplicate')
            ->schema([
                Section::make('Documents obligatoires')
                    ->description('Ces documents sont requis pour finaliser le dossier')
                    ->schema([
                        FileUpload::make('identity_documents')
                            ->label('Pièces d\'identité')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->required(),

                        FileUpload::make('tax_notice')
                            ->label('Avis d\'imposition')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->required(),

                        FileUpload::make('property_documents')
                            ->label('Justificatifs de propriété')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->required(),
                    ])
                    ->columns(1),
                Section::make('Documents optionnels')
                    ->description('Documents complémentaires selon le dossier')
                    ->schema([
                        FileUpload::make('energy_bills')
                            ->label('Factures énergétiques')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120),

                        FileUpload::make('building_permits')
                            ->label('Permis de construire / déclarations')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120),

                        FileUpload::make('technical_reports')
                            ->label('Rapports techniques existants')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120),

                        FileUpload::make('other_documents')
                            ->label('Autres documents')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(5120),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(true),
                Section::make('Validation du dossier')
                    ->schema([
                        CheckboxList::make('document_validations')
                            ->label('Validations obligatoires')
                            ->options([
                                'identity_verified' => 'Identité du client vérifiée',
                                'income_verified' => 'Revenus fiscaux vérifiés',
                                'property_verified' => 'Propriété du bien vérifiée',
                                'eligibility_confirmed' => 'Éligibilité aux aides confirmée',
                                'client_informed' => 'Client informé des modalités',
                            ])
                            ->descriptions([
                                'identity_verified' => 'Les pièces d\'identité correspondent aux informations saisies',
                                'income_verified' => 'L\'avis d\'imposition confirme les revenus déclarés',
                                'property_verified' => 'Les documents prouvent la propriété du bien',
                                'eligibility_confirmed' => 'Le client est éligible aux dispositifs d\'aide',
                                'client_informed' => 'Le client a été informé de toutes les modalités',
                            ])
                            ->required()
                            ->columns(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    // Méthodes pour les champs individuels
    public static function getCivilityField(): Select
    {
        return Select::make('civility')
            ->label('Civilité')
            ->options(['Mr' => 'Monsieur', 'Mrs' => 'Madame'])
            ->required()
            ->native(false);
    }

    public static function getFirstNameField(): TextInput
    {
        return TextInput::make('first_name')
            ->label('Prénom')
            ->required()
            ->maxLength(255)
            ->autocomplete('given-name');
    }

    public static function getLastNameField(): TextInput
    {
        return TextInput::make('last_name')
            ->label('Nom de famille')
            ->required()
            ->maxLength(255)
            ->autocomplete('family-name');
    }

    public static function getFolderNameField(): TextInput
    {
        return TextInput::make('folder_name')
            ->label('Nom du dossier')
            ->required()
            ->maxLength(255)
            ->placeholder('Sera généré automatiquement si laissé vide')
            ->helperText('Format suggéré: NOM_Prénom_Année');
    }

    public static function getFiscalAddressField(): TextInput
    {
        return TextInput::make('fiscal_address')
            ->label('Adresse')
            ->required()
            ->maxLength(255)
            ->autocomplete('street-address');
    }

    public static function getFiscalPostalCodeField(): TextInput
    {
        return TextInput::make('fiscal_postal_code')
            ->label('Code postal')
            ->required()
            ->maxLength(10)
            ->autocomplete('postal-code');
    }

    public static function getFiscalCityField(): TextInput
    {
        return TextInput::make('fiscal_city')
            ->label('Ville')
            ->required()
            ->maxLength(255)
            ->autocomplete('address-level2');
    }

    public static function getFiscalCountryField(): TextInput
    {
        return TextInput::make('fiscal_country')
            ->label('Pays')
            ->required()
            ->default('France')
            ->maxLength(255)
            ->autocomplete('country');
    }

    public static function getPhonesField(): TextInput
    {
        return TextInput::make('phones')
            ->label('Téléphone principal')
            ->tel()
            ->required()
            ->maxLength(255)
            ->autocomplete('tel')
            ->helperText('Format: 01 23 45 67 89');
    }

    public static function getEmailField(): TextInput
    {
        return TextInput::make('email')
            ->label('Adresse email')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(Client::class, 'email')
            ->autocomplete('email');
    }

    public static function getEmailVerifiedAtField(): DateTimePicker
    {
        return DateTimePicker::make('email_verified_at')
            ->label('Email vérifié le')
            ->nullable()
            ->displayFormat('d/m/Y H:i');
    }

    public static function getPasswordField(): TextInput
    {
        return TextInput::make('password')
            ->label('Mot de passe')
            ->password()
          //  ->required()
            ->minLength(8)
            ->confirmed()
            ->autocomplete('new-password')
            ->helperText('Minimum 8 caractères');
    }

    public static function getCanLoginField(): Toggle
    {
        return Toggle::make('can_login')
            ->label('Autoriser la connexion')
            ->helperText('Le client peut-il accéder à son espace personnel ?')
            ->default(false);
    }

    public static function getHouseholdStatusField(): Select
    {
        return Select::make('household_status')
            ->label('Statut du foyer')
            ->options([
                'MODEST_HOUSEHOLDS' => 'Ménages modestes',
                'VERY_MODEST_HOUSEHOLDS' => 'Ménages très modestes',
                'INTERMEDIATE_HOUSEHOLDS' => 'Ménages intermédiaires',
                'SUPERIOR_HOUSEHOLDS' => 'Ménages supérieurs',
            ])
            ->nullable()
            ->disabled() // Sera calculé automatiquement
            ->helperText('Calculé automatiquement en fonction des revenus');
    }

    public static function getUsageTypeField(): Select
    {
        return Select::make('usage_type')
            ->label('Type d\'usage du logement')
            ->options([
                'primary_residence' => 'Résidence principale',
                'owner_occupier' => 'Propriétaire occupant',
                'owner_landlord' => 'Propriétaire bailleur',
                'lender' => 'Prêteur',
                'free_title_occupant' => 'Occupant à titre gratuit',
                'usufructuary' => 'Usufruitier',
                'tenant' => 'Locataire',
                'bare_owner' => 'Nu-propriétaire',
                'sci_owner_occupier' => 'SCI propriétaire occupant',
                'sci_owner_landlord' => 'SCI propriétaire bailleur',
            ])
            ->required()
            ->native(false)
            ->searchable();
    }

    /**
     * Calcule automatiquement la catégorie de foyer selon les barèmes ANAH
     */
    private static function calculateHouseholdCategory(?int $householdSize, ?float $income, Set $set): void
    {
        if (!$householdSize || !$income) {
            $set('household_category', null);
            $set('household_status', null);
            return;
        }
        // Gestion des foyers de plus de 8 personnes
        $effectiveSize = min($householdSize, 8);

        $veryModestThreshold = self::$revenusBaremes['very_modest'][$effectiveSize] ?? null;
        $modestThreshold = self::$revenusBaremes['modest'][$effectiveSize] ?? null;
        if (!$veryModestThreshold || !$modestThreshold) {
            $set('household_category', null);
            $set('household_status', null);
            return;
        }
        // Pour les foyers de plus de 8 personnes, on ajoute le montant par personne supplémentaire
        if ($householdSize > 8) {
            $additionalPersons = $householdSize - 8;
            $veryModestThreshold += $additionalPersons * 6731; // Montant par personne supplémentaire
            $modestThreshold += $additionalPersons * 8101;
        }
        // Détermination de la catégorie
        if ($income <= $veryModestThreshold) {
            $category = 'very_modest';
            $status = 'VERY_MODEST_HOUSEHOLDS';
        } elseif ($income <= $modestThreshold) {
            $category = 'modest';
            $status = 'MODEST_HOUSEHOLDS';
        } elseif ($income <= $modestThreshold * 1.5) { // Seuil intermédiaire approximatif
            $category = 'intermediate';
            $status = 'INTERMEDIATE_HOUSEHOLDS';
        } else {
            $category = 'superior';
            $status = 'SUPERIOR_HOUSEHOLDS';
        }
        $set('household_category', $category);
        $set('household_status', $status);
    }

    /**
     * Retourne les styles CSS personnalisés pour le wizard
     */
    public static function getCustomStyles(): string
    {
        return '
        <style>
        .fi-wizard-vertical-compact {
            --fi-wizard-header-spacing: 1rem;
        }

        .fi-wizard-vertical-compact .fi-wi-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .fi-wizard-vertical-compact .fi-wi-step-label {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .fi-wizard-vertical-compact .fi-wi-step-description {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .fi-wizard-vertical-compact .fi-wi-steps {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .fi-wizard-vertical-compact .fi-wi-step {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .fi-wizard-vertical-compact .fi-wi-step:hover {
            background-color: #f8fafc;
        }

        .fi-wizard-vertical-compact .fi-wi-step.active {
            background-color: #eff6ff;
            border-color: #3b82f6;
            box-shadow: 0 1px 3px rgba(59, 130, 246, 0.1);
        }

        .fi-wizard-vertical-compact .fi-wi-step.completed {
            background-color: #f0fdf4;
            border-color: #22c55e;
        }

        .fi-wizard-vertical-compact .fi-wi-step-icon {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .fi-wizard-vertical-compact .fi-wi-step.active .fi-wi-step-icon {
            background-color: #3b82f6;
            color: white;
        }

        .fi-wizard-vertical-compact .fi-wi-step.completed .fi-wi-step-icon {
            background-color: #22c55e;
            color: white;
        }

        .fi-wizard-vertical-compact .fi-wi-step:not(.active):not(.completed) .fi-wi-step-icon {
            background-color: #e5e7eb;
            color: #6b7280;
        }

        .client-form-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        .fi-section {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .fi-section-header {
            background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
        }

        .fi-placeholder {
            padding: 1rem;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 0.5rem;
            text-align: center;
            font-size: 0.875rem;
        }

        .fi-fo-repeater-item {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }

        .fi-fo-repeater-item:hover {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .fi-wizard-vertical-compact .fi-wi-steps {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }

            .fi-wizard-vertical-compact .fi-wi-step {
                flex-shrink: 0;
                min-width: 200px;
            }
        }
        </style>';
    }
}

// Extension pour la page Create avec le wizard
trait HasClientWizard
{
    use \Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

    protected function getSteps(): array
    {
        return [
            ClientForm::getPersonalInformationStep(),
            ClientForm::getFiscalAddressStep(),
            ClientForm::getProjectAddressStep(),
            ClientForm::getHouseholdCompositionStep(),
            ClientForm::getContractDetailsStep(),
            ClientForm::getMandatesAndAgentsStep(),
            ClientForm::getDocumentsStep(),
        ];
    }

    protected function hasSkippableSteps(): bool
    {
        return false; // Navigation séquentielle obligatoire
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Traitement des données avant sauvegarde

        // Gestion du nom de dossier automatique
        if (empty($data['folder_name'])) {
            $data['folder_name'] = strtoupper($data['last_name']) . '_' .
                                   strtoupper($data['first_name']) . '_' .
                                   date('Y');
        }
        // Copie de l'adresse fiscale vers l'adresse projet si identique
        if ($data['same_as_fiscal_address'] ?? false) {
            $data['project_street'] = $data['fiscal_address'];
            $data['project_postal_code'] = $data['fiscal_postal_code'];
            $data['project_city'] = $data['fiscal_city'];
            $data['project_country'] = $data['fiscal_country'];
        }
        // Transformation du téléphone en JSON pour la base
        if (isset($data['phones']) && is_string($data['phones'])) {
            $data['phones'] = [$data['phones']];
        }
        // Hash du mot de passe
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        // Nettoyage des champs non utilisés
        unset($data['same_as_fiscal_address']);
        unset($data['password_confirmation']);
        unset($data['document_validations']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $client = $this->record;
        // Création de l'adresse du projet si différente
        if ($this->data['project_street'] ?? false) {
            $client->projectAddresses()->create([
                'street' => $this->data['project_street'],
                'postal_code' => $this->data['project_postal_code'],
                'city' => $this->data['project_city'],
                'country' => $this->data['project_country'] ?? 'France',
            ]);
        }
        // Création du contrat
        if ($this->data['project_name'] ?? false) {
            $client->contracts()->create([
                'project_name' => $this->data['project_name'],
                'contract_date' => $this->data['contract_date'],
                'mission_amount_excl_tax' => $this->data['mission_amount_excl_tax'] ?? 1666.67,
                'mission_amount_incl_tax' => $this->data['mission_amount_incl_tax'] ?? 2000.00,
                'vat' => $this->data['vat'] ?? 333.33,
                'first_installment' => $this->data['first_installment'],
                'mar_administrative_agent' => $this->data['mar_administrative_agent'] ?? false,
                'mar_financial_agent' => $this->data['mar_financial_agent'] ?? false,
                'company_name' => $this->data['company_name'],
                'mar_approval_number' => $this->data['mar_approval_number'],
                'siret_number' => $this->data['siret_number'],
                'head_office_address' => $this->data['head_office_address'],
                'head_office_postal_code' => $this->data['head_office_postal_code'],
                'head_office_city' => $this->data['head_office_city'],
                'company_phone' => $this->data['company_phone'],
                'company_email' => $this->data['company_email'],
                'insurer' => $this->data['insurer'],
                'insurance_policy_number' => $this->data['insurance_policy_number'],
                'signature_provider' => $this->data['signature_provider'],
                'signature_provider_id' => $this->data['signature_provider_id'],
                'mar_signature_link' => $this->data['mar_signature_link'],
                'client_signature_link' => $this->data['client_signature_link'],
            ]);
        }
        // Création du mandat avec les informations de composition
        $client->mandates()->create([
            'household_composition' => $this->data['household_composition'],
            'reference_tax_income' => $this->data['reference_tax_income'],
            'household_category' => $this->data['household_category'],
            'cerfa_type' => $this->data['cerfa_type'],
        ]);
        // Création des agents externes
        if (!empty($this->data['external_agents'])) {
            foreach ($this->data['external_agents'] as $agentData) {
                $client->externalAgents()->create($agentData);
            }
        }
        // Gestion des documents uploadés
        $documentTypes = [
            'identity_documents', 'tax_notice', 'property_documents',
            'energy_bills', 'building_permits', 'technical_reports', 'other_documents'
        ];
        foreach ($documentTypes as $docType) {
            if (!empty($this->data[$docType])) {
                $files = is_array($this->data[$docType]) ? $this->data[$docType] : [$this->data[$docType]];

                foreach ($files as $file) {
                    $client->clientDocuments()->create([
                        'document_type' => $docType,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $file->store('client-documents', 'public'),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }
        // Log de l'action
        $client->clientActions()->create([
            'action' => 'dossier_created',
            'description' => 'Dossier client créé via le wizard',
            'data' => [
                'user_id' => auth()->id(),
                'created_via' => 'wizard',
                'steps_completed' => count($this->getSteps()),
            ],
            'created_at' => now(),
        ]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Dossier créé avec succès';
    }

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Dossier client créé')
            ->body('Le dossier de ' . $this->record->first_name . ' ' . $this->record->last_name . ' a été créé avec succès.')
            ->duration(5000)
            ->actions([
                Action::make('view')
                    ->button()
                    ->label('Voir le dossier')
                    ->url(static::getResource()::getUrl('view', ['record' => $this->record])),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->record]);
    }

    public function getTitle(): string
    {
        return 'Nouveau dossier d\'audit énergétique';
    }

    public function getSubheading(): ?string
    {
        return 'Création complète d\'un dossier client avec toutes les informations nécessaires pour l\'audit énergétique et l\'obtention des subventions.';
    }

    // Injection des styles CSS personnalisés
    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'customStyles' => ClientForm::getCustomStyles(),
        ]);
    }
}

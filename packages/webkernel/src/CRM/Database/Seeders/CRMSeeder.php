<?php

namespace Webkernel\CRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkernel\CRM\Models\Pipeline;
use Webkernel\CRM\Models\PipelineStage;
use Webkernel\CRM\Models\ClientType;

class CRMSeeder extends Seeder
{
    public function run(): void
    {
        // Pipeline par défaut pour ReamMar
        $reamMarPipeline = Pipeline::create([
            'name' => 'Pipeline ReamMar Standard',
            'description' => 'Pipeline standard pour les missions de rénovation énergétique',
            'module' => 'ream_mar',
            'is_active' => true,
            'is_default' => true,
            'settings' => [
                'deal_type' => 'mission',
                'currency' => 'EUR',
            ],
        ]);

        // Étapes du pipeline ReamMar
        $stages = [
            [
                'name' => 'Prospect',
                'description' => 'Prospect identifié',
                'order' => 1,
                'win_probability' => 10.00,
                'color' => '#6B7280',
                'is_client_stage' => false,
            ],
            [
                'name' => 'Contact',
                'description' => 'Premier contact établi',
                'order' => 2,
                'win_probability' => 25.00,
                'color' => '#3B82F6',
                'is_client_stage' => false,
            ],
            [
                'name' => 'Opportunité',
                'description' => 'Opportunité qualifiée',
                'order' => 3,
                'win_probability' => 50.00,
                'color' => '#F59E0B',
                'is_client_stage' => false,
            ],
            [
                'name' => 'Client',
                'description' => 'Prospect devenu client',
                'order' => 4,
                'win_probability' => 75.00,
                'color' => '#10B981',
                'is_client_stage' => true,
            ],
            [
                'name' => 'Propriété',
                'description' => 'Propriété évaluée',
                'order' => 5,
                'win_probability' => 80.00,
                'color' => '#8B5CF6',
                'is_client_stage' => false,
            ],
            [
                'name' => 'Audit',
                'description' => 'Audit réalisé',
                'order' => 6,
                'win_probability' => 85.00,
                'color' => '#EC4899',
                'is_client_stage' => false,
            ],
            [
                'name' => 'Financement',
                'description' => 'Plan de financement validé',
                'order' => 7,
                'win_probability' => 90.00,
                'color' => '#06B6D4',
                'is_client_stage' => false,
            ],
            [
                'name' => 'Travaux',
                'description' => 'Travaux en cours',
                'order' => 8,
                'win_probability' => 95.00,
                'color' => '#84CC16',
                'is_client_stage' => false,
            ],
            [
                'name' => 'Finalisation',
                'description' => 'Mission terminée',
                'order' => 9,
                'win_probability' => 100.00,
                'color' => '#059669',
                'is_client_stage' => false,
            ],
        ];

        foreach ($stages as $stageData) {
            PipelineStage::create(array_merge($stageData, [
                'pipeline_id' => $reamMarPipeline->id,
            ]));
        }

        // Types de clients
        ClientType::create([
            'name' => 'Client ReamMar',
            'description' => 'Client du module ReamMar',
            'model_type' => 'Numerimondes\Modules\ReamMar\Core\Models\Client',
            'module' => 'ream_mar',
            'is_active' => true,
            'capabilities' => [
                'can_receive_quotes',
                'can_schedule_audits',
                'can_request_financing',
                'can_track_works',
            ],
        ]);

        ClientType::create([
            'name' => 'Utilisateur Webkernel',
            'description' => 'Utilisateur standard de Webkernel',
            'model_type' => 'App\Models\User',
            'module' => 'webkernel',
            'is_active' => true,
            'capabilities' => [
                'can_access_platform',
                'can_manage_modules',
                'can_view_reports',
            ],
        ]);
    }
} 
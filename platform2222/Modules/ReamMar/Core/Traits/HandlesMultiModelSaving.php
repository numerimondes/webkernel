<?php

namespace Numerimondes\Modules\ReamMar\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HandlesMultiModelSaving
{
    /**
     * Charge les données des modèles liés
     */
    protected function loadRelatedModels(array $data, array $modelMappings): array
    {
        foreach ($modelMappings as $key => $modelClass) {
            $relation = $this->record->{$key};
            
            if ($relation) {
                $relatedData = $relation->toArray();
                unset($relatedData['id'], $relatedData['created_at'], $relatedData['updated_at']);
                
                foreach ($relatedData as $field => $value) {
                    $data["{$key}.{$field}"] = $value;
                }
            }
        }
        
        return $data;
    }

    /**
     * Sauvegarde les données dans les modèles appropriés
     */
    protected function saveMultipleRelatedModels(array $data, array $modelMappings): array
    {
        $missionData = [];
        
        foreach ($data as $key => $value) {
            if (str_contains($key, '.')) {
                [$modelKey, $field] = explode('.', $key, 2);
                
                if (isset($modelMappings[$modelKey])) {
                    // Données pour un modèle lié
                    if (!isset($missionData[$modelKey])) {
                        $missionData[$modelKey] = [];
                    }
                    $missionData[$modelKey][$field] = $value;
                } else {
                    // Données pour la mission principale
                    $missionData[$key] = $value;
                }
            } else {
                // Données pour la mission principale
                $missionData[$key] = $value;
            }
        }
        
        // Sauvegarder les modèles liés
        foreach ($modelMappings as $key => $modelClass) {
            if (isset($missionData[$key])) {
                $this->saveRelatedModel($key, $modelClass, $missionData[$key]);
            }
        }
        
        // Retourner seulement les données de la mission
        return Arr::except($missionData, array_keys($modelMappings));
    }

    /**
     * Sauvegarde un modèle lié
     */
    protected function saveRelatedModel(string $relationKey, string $modelClass, array $data): void
    {
        $relation = $this->record->{$relationKey};
        
        if ($relation) {
            // Mettre à jour le modèle existant
            $relation->update($data);
        } else {
            // Créer un nouveau modèle
            $newModel = new $modelClass($data);
            $newModel->save();
            
            // Associer au modèle principal
            $this->record->{$relationKey}()->associate($newModel);
            $this->record->save();
        }
    }

    /**
     * Valide les données multi-modèles
     */
    protected function validateMultiModelData(array $data, array $modelMappings): array
    {
        $errors = [];
        
        foreach ($modelMappings as $key => $modelClass) {
            $modelData = [];
            
            foreach ($data as $field => $value) {
                if (str_starts_with($field, "{$key}.")) {
                    $modelField = substr($field, strlen("{$key}."));
                    $modelData[$modelField] = $value;
                }
            }
            
            if (!empty($modelData)) {
                $model = new $modelClass();
                $validator = validator($modelData, $model->getValidationRules());
                
                if ($validator->fails()) {
                    foreach ($validator->errors()->getMessages() as $field => $messages) {
                        $errors["{$key}.{$field}"] = $messages;
                    }
                }
            }
        }
        
        return $errors;
    }

    /**
     * Nettoie les données avant sauvegarde
     */
    protected function cleanMultiModelData(array $data, array $modelMappings): array
    {
        $cleanedData = [];
        
        foreach ($data as $key => $value) {
            if (str_contains($key, '.')) {
                [$modelKey, $field] = explode('.', $key, 2);
                
                if (isset($modelMappings[$modelKey])) {
                    // Garder les données des modèles liés
                    $cleanedData[$key] = $value;
                }
            } else {
                // Garder les données de la mission
                $cleanedData[$key] = $value;
            }
        }
        
        return $cleanedData;
    }
} 
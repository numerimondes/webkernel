<?php

namespace Webkernel\Aptitudes\Panels\Http\Controllers;

/**
 * Purpose: RESTful controller for managing dynamic panel configurations
 *
 * This controller provides CRUD operations for panel configurations,
 * integrating with the PanelSchemaHelper for validation and providing
 * endpoints for schema inspection and panel testing.
 *
 * Features:
 * - Standard CRUD operations for panels
 * - Schema endpoint for dynamic form generation
 * - Validation using dynamic schema analysis
 * - Panel testing and debugging endpoints
 */

use App\Http\Controllers\Controller;
use Webkernel\Aptitudes\Panels\Http\Requests\StorePanelsRequest;
use Webkernel\Aptitudes\Panels\Http\Requests\UpdatePanelsRequest;
use Webkernel\Aptitudes\Panels\Models\Panels;
use Webkernel\Aptitudes\Panels\Helpers\PanelSchemaHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PanelsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Panels::query();

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        if ($request->has('source')) {
            $query->bySource($request->input('source'));
        }

        $panels = $query->orderBy('sort_order')
                       ->orderBy('id')
                       ->get();

        return response()->json([
            'success' => true,
            'data' => $panels,
            'count' => $panels->count()
        ]);
    }

    public function store(StorePanelsRequest $request): JsonResponse
    {
        try {
            $panel = Panels::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Panel created successfully',
                'data' => $panel
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create panel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Panels $panel): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $panel
        ]);
    }

    public function update(UpdatePanelsRequest $request, Panels $panel): JsonResponse
    {
        try {
            $panel->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Panel updated successfully',
                'data' => $panel
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update panel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Panels $panel): JsonResponse
    {
        try {
            $panel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Panel deleted successfully'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete panel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle panel active status
     */
    public function toggle(Panels $panel): JsonResponse
    {
        try {
            $panel->update(['is_active' => !$panel->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Panel status toggled successfully',
                'data' => $panel
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle panel status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set panel as default
     */
    public function setDefault(Panels $panel): JsonResponse
    {
        try {
            // Remove default from all panels
            Panels::query()->update(['is_default' => false]);

            // Set this panel as default
            $panel->update(['is_default' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Panel set as default successfully',
                'data' => $panel
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set panel as default',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

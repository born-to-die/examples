<?php

namespace App\BTU\TAS\Http\Controllers\API\Planning;

use App\BTU\TAS\Http\Requests\API\PlanningController\PlanningSetRequest;
use App\BTU\TAS\Http\Requests\API\PlanningController\PlanningUpdateRequest;
use App\BTU\TAS\Models\Project;
use App\BTU\TAS\Services\Planning\PlanningService;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Класс-контроллер для выставления проекту "На планировании"
 */
class PlanningController extends Controller
{
    /**
     * Создание проекта и возвращение его в виде ресурса
     * @param aopAorConverterStoreRequest $request
     * @return aorProjectResource
     */
    public function setPlanning(PlanningSetRequest $request)
    {
        $aop_project = Project::where('created_by', Auth::user()->id)->findOrFail($request->aop_project_id);
        $aop_project->is_planning = true;
        $aop_project->save();
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Обновляет данные планирования для проекта
     * @param PlanningUpdateRequest $request
     * @return JsonResponse
     */
    public function updatePlanning(PlanningUpdateRequest $request, PlanningService $service): JsonResponse
    {
        return $service->handlePerformers($request);
    }
}

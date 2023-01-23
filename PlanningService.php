<?php

/**
 * Сервис планировщика
 * Нужен для обработки данных связанных с планированием
 * 
 * @category Services
 */

namespace App\BTU\TAS\Services\Planning;

use App\BTU\TAS\Http\Requests\API\PlanningController\PlanningUpdateRequest;
use App\BTU\TAS\Contracts\Planning\Planning;
use App\BTU\TAS\Models\Performers\CrossPerformer;
use App\BTU\TAS\Models\Performers\DboardPerformer;
use App\BTU\TAS\Models\Performers\FocablePerformer;
use App\BTU\TAS\Models\Performers\KnotPerformer;
use App\BTU\TAS\Models\Performers\OmPerformer;

use Illuminate\Http\JsonResponse;

/**
 * Сервис для обработки действий над планированием проекта
 * 
 * @category Services
 */
class PlanningService implements Planning
{
    /**
     * Общий метод для обновлени данных с запроса на планировании
     * 
     * Служит для CRUD-операций над исполнителями на постановке-планировании
     * 
     * @param PlanningUpdateRequest $request Запрос с проверенными данными
     * 
     * @return JsonResponse
     */
    public function handlePerformers(PlanningUpdateRequest $request): JsonResponse
    {
        $response_array = [];
        $performers = [];

        if (isset($request['aor_knots'])) {
            $performers['aor_knots'] = $this->handleKnots(
                $request['aor_knots'],
                $response_array
            );
        }

        if (isset($request['aor_oms'])) {
            $performers['aor_oms'] = $this->handleOms(
                $request['aor_oms'],
                $response_array
            );
        }

        if (isset($request['aor_dboards'])) {
            $performers['aor_dboards'] = $this->handleDboards(
                $request['aor_dboards'],
                $response_array
            );
        }

        if (isset($request['aor_focables'])) {
            $performers['aor_focables'] = $this->handleFocables(
                $request['aor_focables'],
                $response_array
            );
        }

        $response_array = [
            'status' => 'success',
            'performers' => $performers,
        ];

        return response()->json($response_array, 201);
    }

    /**
     * Метод для CRUD-операций над исполнителями узлов связи
     * 
     * Выполняет действия над исполнителям ина узлах связи, также 
     * совершает операции над включёнными в узлы связи кроссами и их исполни-ми
     * 
     * @param array $knots_data Массив данных с узлами связи
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив узлов связи с с созданными исполнителями
     */
    public function handleKnots(
        array $knots_data,
        array &$response_array
    ): array
    {
        $performers = [];

        if (isset($knots_data['update'])) {
            $performers['update'] = $this->handleKnotsPerformers($knots_data['update'], $response_array);
        }

        return $performers;
    }

    /**
     * Метод для CRUD-операций над исполнителями на узлу связи
     * 
     * @param array $knots_data Массив с данными узлов
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей
     */
    public function handleKnotsPerformers(
        array $knots_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($knots_data as $knot_data) {

            if (isset($knot_data['cupboard_users']['create'])) {
                $performers['cupboard_users']['create'] = $this->createKnotsPerformers(
                    $knot_data,
                    $knot_data['cupboard_users']['create'],
                    $response_array
                );
            }

            if (isset($knot_data['passive_optical_equipments'])) {
                $performers['passive_optical_equipments'] = 
                    $this->handleCrosses($knot_data['passive_optical_equipments'], $response_array);
            }
        }

        return $performers;
    }

    /**
     * Метод для перебора и узлов и создания на них исполнителей
     * 
     * Проходит по всем узлам в массиве, и вызывает для каждого CRUD-метод исп
     * 
     * @param array $knot Массив данных узла связи
     * @param array $performers Массив исполнителей
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей, а также массив с исп-лями на кроссах
     */
    public function createKnotsPerformers(
        array $knot,
        array $performers_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($performers_data as $performer_data) {
            $knot_performer = new KnotPerformer();
            $knot_performer->project_knot_id = $knot['id'];
            $knot_performer->btu_user_login = $performer_data['btu_user_login'];
            $knot_performer->save();
            $performers[] = $knot_performer;
        }

        return $performers;
    }

    /**
     * Метод для оепараций для кроссов
     * 
     * @param array $crosses_data Массив данных с кроссами и действиями
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив узлов связи с исполнителями
     */
    public function handleCrosses(
        array $crosses_data,
        array &$response_array
    ): array
    {
        $performers = [];

        if (isset($crosses_data['update'])) {
            $performers['update'] = $this->handleCrossesPerformers($crosses_data['update'], $response_array);
        }

        return $performers;
    }

    /**
     * Метод для CRUD-операций над исполнителями на кроссах
     * 
     * @param array $cross_data Массив данных узла связи с кроссами
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Масси исполнителей на кроссах 
     */
    public function handleCrossesPerformers(
        array $crosses_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($crosses_data as $cross_data) {

            if (isset($cross_data['performers']['create'])) {
                $performers['performers']['create'] = $this->createCrossesPerformers(
                    $cross_data,
                    $cross_data['performers']['create'],
                    $response_array
                );
            }
        }

        return $performers;
    }

    /**
     * Метод для перебора и кроссов и создания на них исполнителей
     * 
     * Проходит по всем кроссам в массиве, и вызывает для каждого CRUD-метод исп
     * 
     * @param array $cross Массив данных кросса
     * @param array $performers_data Массив исполнителей
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей, а также массив с исп-лями на кроссах
     */
    public function createCrossesPerformers(
        array $cross,
        array $performers_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($performers_data as $performer_data) {
            $cross_performer = new CrossPerformer();
            $cross_performer->project_knot_cross_id = $cross['id'];
            $cross_performer->btu_user_login = $performer_data['btu_user_login'];
            $cross_performer->save();
            $performers[] = $cross_performer;
        }

        return $performers;
    }

    /**
     * Метод для операций над муфтами
     * 
     * @param array $oms_data Массив данных с муфтами
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив узлов связи с исполнителями
     */
    public function handleOms(
        array $oms_data,
        array &$response_array
    ): array
    {
        $performers = [];

        if (isset($oms_data['update'])) {
            $performers['update'] = $this->handleOmsPerformers($oms_data['update'], $response_array);
        }

        return $performers;
    }

    /**
     * Метод для CRUD-операций над исполнителями на муфтах
     * 
     * @param array $oms_data Массив с данными муфт
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей
     */
    public function handleOmsPerformers(
        array $oms_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($oms_data as $om_data) {

            if (isset($om_data['performers']['create'])) {
                $performers['performers']['create'] = $this->createOmsPerformers(
                    $om_data,
                    $om_data['performers']['create'],
                    $response_array
                );
            }
        }

        return $performers;
    }

    /**
     * Метод для создания исполнителей на муфтах
     * 
     * @param array $om Массив данных муфты
     * @param array $performers_data Массив исполнителей
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей
     */
    public function createOmsPerformers(
        array $om,
        array $performers_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($performers_data as $performer_data) {
            $om_performer = new OmPerformer();
            $om_performer->aop_om_id = $om['id'];
            $om_performer->btu_user_login = $performer_data['btu_user_login'];
            $om_performer->save();
            $performers[] = $om_performer;
        }

        return $performers;
    }

    /**
     * Метод для операций над шкафами
     * 
     * @param array $dboards_data Массив данных со шкафами
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив шкафов с исполнителями
     */
    public function handleDboards(
        array $dboards_data,
        array &$response_array
    ): array
    {
        $performers = [];

        if (isset($dboards_data['update'])) {
            $performers['update'] = $this->handleDboardsPerformers($dboards_data['update'], $response_array);
        }

        return $performers;
    }

    /**
     * Метод для CRUD-операций над исполнителями на шкафах
     * 
     * @param array $dboards_data Массив с данными шкафов
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей
     */
    public function handleDboardsPerformers(
        array $dboards_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($dboards_data as $dboard_data) {

            if (isset($dboard_data['performers']['create'])) {
                $performers['performers']['create'] = $this->createDboardsPerformers(
                    $dboard_data,
                    $dboard_data['performers']['create'],
                    $response_array
                );
            }
        }

        return $performers;
    }

    /**
     * Метод для создания исполнителей на шкафах
     * 
     * @param array $dboard Массив данных шкафа
     * @param array $performers_data Массив исполнителей
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей
     */
    public function createDboardsPerformers(
        array $dboard,
        array $performers_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($performers_data as $performer_data) {
            $dboard_performer = new DboardPerformer();
            $dboard_performer->aop_dboard_id = $dboard['id'];
            $dboard_performer->btu_user_login = $performer_data['btu_user_login'];
            $dboard_performer->save();
            $performers[] = $dboard_performer;
        }

        return $performers;
    }

    /**
     * Метод для операций над кабелями
     * 
     * @param array $focables_data Массив данных с кабелями
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив шкафов с исполнителями
     */
    public function handleFocables(
        array $focables_data,
        array &$response_array
    ): array
    {
        $performers = [];

        if (isset($focables_data['update'])) {
            $performers['update'] = $this->handleFocablesPerformers($focables_data['update'], $response_array);
        }

        return $performers;
    }

    /**
     * Метод для CRUD-операций над исполнителями на кабелях
     * 
     * @param array $focables_data Массив с данными кабелей
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей
     */
    public function handleFocablesPerformers(
        array $focables_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($focables_data as $focable_data) {

            if (isset($focable_data['performers']['create'])) {
                $performers['performers']['create'] = $this->createFocablesPerformers(
                    $focable_data,
                    $focable_data['performers']['create'],
                    $response_array
                );
            }
        }

        return $performers;
    }

    /**
     * Метод для создания исполнителей на кабелях
     * 
     * @param array $focable Массив данных кабеля
     * @param array $performers_data Массив исполнителей
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей
     */
    public function createFocablesPerformers(
        array $focable,
        array $performers_data,
        array &$response_array
    ): array
    {
        $performers = [];

        foreach ($performers_data as $performer_data) {
            $focable_performer = new FocablePerformer();
            $focable_performer->aop_focable_id = $focable['id'];
            $focable_performer->btu_user_login = $performer_data['btu_user_login'];
            $focable_performer->save();
            $performers[] = $focable_performer;
        }

        return $performers;
    }
}

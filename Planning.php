<?php

namespace App\BTU\TAS\Contracts\Planning;

use App\BTU\TAS\Http\Requests\API\PlanningController\PlanningUpdateRequest;
use App\BTU\TAS\Models\Performers\KnotPerformer;
use App\BTU\TAS\Models\ProjectKnot;
use Illuminate\Http\JsonResponse;

/**
 * Интерфейс для планировщика
 */
interface Planning
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
    public function handlePerformers(PlanningUpdateRequest $request): JsonResponse;

    /**
     * Метод для оепараций на узлу связи
     * 
     * @param array $knots_data Массив данных с узлами связи
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив узлов связи с исполнителями
     */
    public function handleKnots(array $knots_data, array &$response_array): array;

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
    ): array;

    /**
     * Метод для перебора и узлов и создания на них исполнителей
     * 
     * Проходит по всем узлам в массиве, и вызывает для каждого CRUD-метод исп
     * 
     * @param array $knot Массив данных узла связи
     * @param array $performers_data Массив исполнителей
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив исполнителей, а также массив с исп-лями на кроссах
     */
    public function createKnotsPerformers(
        array $knot,
        array $performers_data,
        array &$response_array
    ): array;

    /**
     * Метод для оепараций для кроссов
     * 
     * @param array $crosses_data Массив данных с кроссами и действиями
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив узлов связи с исполнителями
     */
    public function handleCrosses(array $crosses_data, array &$response_array): array;

    /**
     * Метод для CRUD-операций над исполнителями на кроссах
     * 
     * @param array $crosses_data Массив данных узла связи с кроссами
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Масси исполнителей на кроссах 
     */
    public function handleCrossesPerformers(
        array $crosses_data,
        array &$response_array
    ): array;

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
    ): array;

    /**
     * Метод для операций над муфтами
     * 
     * @param array $oms_data Массив данных с муфтами
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив узлов связи с исполнителями
     */
    public function handleOms(array $oms_data, array &$response_array): array;

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
    ): array;

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
    ): array;

    /**
     * Метод для операций над шкафами
     * 
     * @param array $dboards_data Массив данных со шкафами
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив шкафов с исполнителями
     */
    public function handleDboards(array $dboards_data, array &$response_array): array;

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
    ): array;

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
    ): array;

    /**
     * Метод для операций над кабелями
     * 
     * @param array $focables_data Массив данных с кабелями
     * @param array &$response_array Массив для ответа на запрос
     * 
     * @return array Массив шкафов с исполнителями
     */
    public function handleFocables(array $focables_data, array &$response_array): array;

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
    ): array;

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
    ): array;
}
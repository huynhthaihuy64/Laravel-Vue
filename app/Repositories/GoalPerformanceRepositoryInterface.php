<?php

namespace App\Repositories;

use App\Enums\Performance\TypeUnit;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface GoalPerformanceRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Check Exists Goal Performance
     *
     * @param array $data
     * @return array
     */
    public function getGoalPerformance(array $data): array;

    /**
     * Query list goal performance
     *
     * @param array $data
     * @return Collection|LengthAwarePaginator
     */
    public function getList(array $data): Collection|LengthAwarePaginator;

    /**
     * Update By Condition
     *
     * @param array $data
     * @return bool
     */
    public function updateByCondition(array $data): bool;

    /**
     * List performance one unit
     *
     * @param array $data
     * @return array
     */
    public function listPerformanceOneUnit(array $data): array;

    /**
     * List performance of many unit
     *
     * @param array $data
     * @return array
     */
    public function listPerformanceOfManyUnit(array $data): array;


    /**
     * @param int $id
     * @param array $condition
     * @return bool
     */
    public function updateById(int $id, array $condition): bool;

    /**
     * List unit goal in goal performance
     *
     * @param array $data
     * @return array
     */
    public function getListUnitGoal(array $data): array;

    /**
     * Get goal performance grouped by goal.
     *
     * @param int $assigneeId Id of assignee
     * @param TypeUnit $type_unit Type unit of assignee, whether user or workspace
     * @param string|int $month
     * @param string|int $year
     * @return void
     */
    public function getGroup(int $assigneeId, TypeUnit $type_unit, string|int $month, string|int $year): Collection;
}

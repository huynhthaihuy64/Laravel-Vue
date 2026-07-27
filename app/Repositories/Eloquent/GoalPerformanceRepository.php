<?php

namespace App\Repositories\Eloquent;

use App\Constants\Constants;
use App\Enums\GoalPerformance\ScreenPerformance;
use App\Enums\Performance\PerformanceProgress;
use App\Enums\Performance\PerformanceStatus;
use App\Enums\Performance\PerformanceType;
use App\Enums\Performance\TypeUnit;
use App\Models\GoalPerformance;
use App\Repositories\GoalPerformanceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GoalPerformanceRepository extends BaseRepository implements GoalPerformanceRepositoryInterface
{
    /**
     * @param GoalPerformance $model
     */
    public function __construct(GoalPerformance $model)
    {
        parent::__construct($model);
    }

    /** Check exists goal performance
     *
     * @param array $data
     * @return array
     */
    public function getGoalPerformance(array $data): array
    {
        $result = $this->model
            ->where('goal_id', $data['goal_id'])
            ->where('assignee_id', $data['assignee_id'])
            ->where('type', $data['type'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->where('type_unit', $data['type_unit'])
            ->first();
        return  $result ? $result->toArray() : [];
    }

    /**
     * Query list goal performance
     *
     * @param array $data
     * @return Collection|LengthAwarePaginator
     */
    public function getList(array $data): Collection|LengthAwarePaginator
    {
        if (!empty($data['month']) && strpos($data['month'][0], '/')) {
            $data['year'] = (int)explode('/', $data['month'][0])[1];
            $data['month'] = array_map(fn ($item) => (int)explode('/', $item)[0], $data['month']);
        } elseif (!empty($data['month'])) {
            $data['year'] = (int)$data['month'][0];
            unset($data['month']);
        }
        $query = $this->model->query()
            ->rightJoin('goals', 'goal_performances.goal_id', 'goals.id');
        if ((int)$data['type_unit'] === TypeUnit::WORKSPACE->value) {
            $query->rightJoin('workspaces', 'goal_performances.assignee_id', 'workspaces.id')
                ->with('workspace');
            $selectFields = [
                'workspaces.code as workspace_code',
                'workspaces.name as workspace_name',
            ];
            $code = 'workspaces.code';
            $name = 'workspaces.name';
        } else {
            $query->rightJoin('users', 'goal_performances.assignee_id', 'users.id')
                ->with('user');
            $selectFields = [
                'users.employee_code',
                'users.name',
                'users.user_name',
            ];
            $code = 'users.employee_code';
            $name = 'users.user_name';
        }
        $query->where('goal_performances.type_unit', $data['type_unit']);
        if (!empty($data['goal_id'])) {
            $query->whereIn('goal_performances.goal_id', $data['goal_id']);
        }
        if (!empty($data['status'])) {
            $query->whereIn('goal_performances.status', $data['status']);
        }
        if (isset($data['assignee_id']) && $data['assignee_id']) {
            $data['assignee_id'] = explode(",", $data['assignee_id']);
            $query->whereIn('goal_performances.assignee_id', $data['assignee_id']);
        }
        if (isset($data['month'])) {
            $query->whereIn('goal_performances.month', $data['month']);
        }
        if (isset($data['year'])) {
            $query->where('goal_performances.year', $data['year']);
        }
        if (isset($data['type']) && $data['type']) {
            $query->where('goal_performances.type', $data['type']);
        }
        if (isset($data['assignee_code'])) {
            $query->where(function ($query) use ($data, $code, $name) {
                $query->orWhere($code, 'LIKE', "%{$data['assignee_code']}%")
                    ->orWhere($name, 'LIKE', "%{$data['assignee_code']}%")
                    ->orWhere('goals.code', 'LIKE', "%{$data['assignee_code']}%")
                    ->orWhere('goals.name', 'LIKE', "%{$data['assignee_code']}%");
            });
        }
        if (isset($data['screen']) && (int)$data['screen'] === ScreenPerformance::RESULT_PERFORMANCE->value) {
            $query->leftJoin('performances', 'goal_performances.assignee_id', 'performances.assignee_id')
                ->where('performances.type_unit', $data['type_unit'])
                ->whereIn('performances.month', $data['month'])
                ->where('performances.year', $data['year'])
                ->where('performances.type', $data['type'])
                ->whereNotNull('goal_performances.original_value')
                ->whereNull('performances.deleted_at');
            array_push($selectFields, 'performances.progress');
        }

        if (isset($data['is_approved']) && $data['is_approved']) {
            $query->where('performances.progress', PerformanceProgress::APPROVED->value);
        } elseif (isset($data['is_approved']) && !$data['is_approved']) {
            $query->where('performances.progress', '!=', PerformanceProgress::APPROVED->value);
        }

        $query->select(array_merge([
            'goal_performances.*',
            'goals.code as goal_code',
            'goals.name as goal_name',
        ], $selectFields))
            ->with('goal')
            ->orderBy('goal_performances.updated_at', 'DESC')
            ->orderBy('goal_performances.id', 'DESC');

        if (!empty($data['per_page']) || !empty($data['page'])) {
            $page = !empty($data['page']) ? $data['page'] : Constants::DEFAULT_PAGE;
            $perPage = !empty($data['per_page']) ? $data['per_page'] : Constants::DEFAULT_PER_PAGE;
            $total = $query->count();
            $listGoalPerformances = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();
            return new LengthAwarePaginator($listGoalPerformances, $total, $perPage, $page);
        }

        return $query->get();
    }

    /**
     * Update By Condition
     *
     * @param array $data
     * @return bool
     */
    public function updateByCondition(array $data): bool
    {
        return $this->model->where('assignee_id', $data['assignee_id'])
            ->where('type', $data['type'])
            ->where('type_unit', $data['type_unit'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->update(['status' => $data['status'], 'modifier_id' => $data['modifier_id']]);
    }

    /**
     * updateById
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateById(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    /**
     * Get list unit goal in goal performance
     *
     * @param array $data
     * @return array
     */
    public function getListUnitGoal(array $data): array
    {
        $query = $this->model->query()->where('type_unit', $data['type_unit']);
        if (isset($data['is_goal']) && $data['is_goal']) {
            $result = $query
                ->join('goals', 'goals.id', 'goal_performances.goal_id')
                ->where('goal_performances.assignee_id', $data['assignee_id'])
                ->select('goals.id', 'goals.name')
                ->groupBy('goals.id', 'goals.name')
                ->orderBy('goals.name', 'ASC')
                ->get();
            return array_values($result->toArray());
        } elseif ((int)$data['type_unit'] === TypeUnit::USER->value) {
            $query->join('users', 'users.id', 'goal_performances.assignee_id')
                ->where('goal_performances.type_unit', $data['type_unit']);
            if (!empty($data['status'])) {
                $query->whereIn('users.status', $data['status']);
            }
            $query->select('users.id', 'users.name', 'users.user_name', 'users.full_name')
                ->orderBy('users.user_name', 'ASC')
                ->distinct();
            $result = $query->get();
        } else {
            $result = $query
                ->whereHas('workspace', function ($query) use ($data) {
                    !empty($data['status']) ? $query->whereIn('status', $data['status']) : null;
                })
                ->pluck('assignee_id');
        }
        return array_values(array_filter($result->unique()->toArray()));
    }

    /**
     * List performance one unit
     *
     * @param array $data
     * @return array
     */
    public function listPerformanceOneUnit(array $data): array
    {
        $query = $this->model->query();
        $query->where('goal_performances.type', $data['type'])
            ->where('goal_performances.assignee_id', $data['assignee_id'])
            ->where('goal_performances.type_unit', $data['type_unit']);
        if ((int)$data['type_unit'] === TypeUnit::USER->value) {
            $query->rightJoin('users', 'goal_performances.assignee_id', 'users.id');
        } else {
            $query->rightJoin('workspaces', 'goal_performances.assignee_id', 'workspaces.id');
        }
        if (isset($data['month'])) {
            $query->whereIn(DB::raw('CONCAT(goal_performances.month, "/", goal_performances.year)'), $data['month']);
        }
        if (!empty($data['status'])) {
            $query->whereIn('goal_performances.status', $data['status']);
        }

        //List of goal ids with data in the performance set list
        $idGoals = $query->get()->pluck('goal_id')->unique()->toArray();

        $result = $query
            ->orderBy('goal_performances.year', 'ASC')
            ->orderBy('goal_performances.month', 'ASC')
            ->select(
                'goal_performances.month',
                'goal_performances.year',
                'goal_performances.updated_at',
                'goal_performances.status',
                'goal_performances.proportion',
                'goal_performances.goal_id',
                'goal_performances.type',
                DB::raw('CONCAT(goal_performances.month, "/", goal_performances.year) as time')
            )
            ->get()
            ->groupBy('time')
            ->map(function ($group) use ($data) {
                $latest = $group->sortByDESC('updated_at')->first();
                if (!empty($data['status']) && !in_array($latest['status'], $data['status'])) {
                    return [];
                } else {
                    return [
                        "data" => $group,
                        "latest_status" => $latest['status']
                    ];
                }
            });
        $result = $result->filter()->toArray();
        $result['ids'] = $idGoals;
        return $result;
    }

    /**
     * List performance of many unit
     *
     * @param array $data
     * @return array
     */
    public function listPerformanceOfManyUnit(array $data): array
    {
        $query = $this->model->query()
            ->rightJoin('goals', 'goal_performances.goal_id', 'goals.id');
        if ((int)$data['type_unit'] === TypeUnit::WORKSPACE->value) {
            $query->rightJoin('workspaces', 'goal_performances.assignee_id', 'workspaces.id');
        } else {
            $query->rightJoin('users', 'goal_performances.assignee_id', 'users.id')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id');
        }
        $query->where('goal_performances.type_unit', $data['type_unit']);
        if (isset($data['assignee_id']) && $data['assignee_id'] !== null) {
            $data['assignee_id'] = explode(",", $data['assignee_id']);
            $query->whereIn('goal_performances.assignee_id', $data['assignee_id']);
        }
        $query->where('goal_performances.type', $data['type']);
        $query->where('goal_performances.month', $data['month']);
        $query->where('goal_performances.year', $data['year']);
        $query->with('goal.goalCategory');
        $query->select(
            'goal_performances.goal_id',
            'goal_performances.month',
            'goal_performances.year',
            'goal_performances.proportion',
            'goal_performances.assignee_id',
            'goal_performances.updated_at',
            'goal_performances.status'
        );
        if ((int)$data['type_unit'] === TypeUnit::USER->value) {
            $query->orderByRaw("ISNULL(roles.level), roles.level ASC")->orderBy('users.name', 'ASC');
        } else {
            $query->orderBy('workspaces.name', 'ASC');
        }
        return $query->get()
            ->groupBy('assignee_id')
            ->map(function ($group) use ($data) {
                $latestStatus = $group->sortByDesc('updated_at')->first()->status;
                if (isset($data['status']) && !in_array($latestStatus, $data['status'])) {
                    return [];
                } else {
                    return [
                        "data" => $group,
                        "latest_status" => $latestStatus
                    ];
                }
            })
            ->filter()
            ->toArray();
    }

    /**
     * Get goal performance grouped by goal.
     *
     * @param int $assigneeId Id of assignee
     * @param TypeUnit $type_unit Type unit of assignee, whether user or workspace
     * @param string|int $month
     * @param string|int $year
     * @return void
     */
    public function getGroup(int $assigneeId, TypeUnit $type_unit, string|int $month, string|int $year): Collection
    {
        return $this->findByCondition(['goal'], [
            'assignee_id' => $assigneeId,
            'month' => $month,
            'year' => $year,
            'type_unit' => $type_unit->value,
            'status' => PerformanceStatus::APPROVED->value,
            ['value', '<>', null]
        ])
            ->groupBy('goal_id')
            ->map(function ($grouped) {
                $goal = $grouped->first()->goal;
                $goal->setAttribute('yearlyGoalPerformance', $grouped->where('type', PerformanceType::YEAR->value)->first());
                $goal->setAttribute('monthlyGoalPerformance', $grouped->where('type', PerformanceType::MONTH->value)->first());
                return $goal;
            })->values();
    }
}

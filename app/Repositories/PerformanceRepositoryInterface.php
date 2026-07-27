<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface PerformanceRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get Performance
     *
     * @param array $data
     * @return array
     */
    public function getPerformance(array $data): array;

    /**
     * Get list performances
     *
     * @param array $data
     * @param int $type
     * @param bool $isAcceptValue
     * @return Builder
     */
    public function getListPerformances(array $data, int $type, bool $isAcceptValue = false): Builder;
}

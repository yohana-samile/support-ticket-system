<?php

namespace App\Repositories\Frontend\Interfaces;

use App\Models\Incident;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface IncidentRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Incident;
    public function create(array $data): Incident;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByStatus(string $status): Collection;
    public function getByType(string $type): Collection;
    public function getByReporter(int $reporterId): Collection;
    public function addCaseUpdate(int $incidentId, array $updateData): void;
    public function attachSupportServices(int $incidentId, array $serviceIds, array $pivotData = []): void;
}

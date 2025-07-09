<?php
namespace App\Repositories\Frontend\Eloquent;

use App\Models\Incident;
use App\Models\System\CodeValue;
use App\Models\Victim;
use App\Models\Perpetrator;
use App\Models\Evidence;
use App\Repositories\Frontend\Interfaces\IncidentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncidentRepository implements IncidentRepositoryInterface
{
    public function all(): Collection
    {
        return Incident::with(['victims', 'perpetrators', 'evidence'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Incident::with(['victims', 'perpetrators'])->latest()->paginate($perPage);
    }

    public function find(int $id): ?Incident
    {
        return Incident::with(['victims', 'perpetrators', 'evidence', 'supportServices', 'updates.user'])->findOrFail($id);
    }

    public function incidentsByType()
    {
        return Incident::with('statusModel')->get()->groupBy('type');
    }

    public function create(array $data): Incident
    {
        return DB::transaction(function () use ($data) {
            $caseStatus = CodeValue::query()->where('reference', "CASE01")->value("name");
            $incident = Incident::create([
                'reporter_id' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'],
                'occurred_at' => $data['occurred_at'],
                'location' => $data['location'],
                'type' => $data['type'],
                'status' => $caseStatus,
                'is_anonymous' => $data['is_anonymous'] ?? false,
                'uid' => Str::uuid()
            ]);

            foreach ($data['victims'] as $victimData) {
                $incident->victims()->create($victimData);
            }

            if (isset($data['perpetrators'])) {
                foreach ($data['perpetrators'] as $perpetratorData) {
                    $incident->perpetrators()->create($perpetratorData);
                }
            }

            if (isset($data['evidence'])) {
                foreach ($data['evidence'] as $evidenceData) {
                    $path = $evidenceData['file']->store('evidence', 'public');
                    $incident->evidence()->create([
                        'file_path' => $path,
                        'file_type' => $evidenceData['file']->getClientOriginalExtension(),
                        'description' => $evidenceData['description'] ?? null,
                    ]);
                }
            }

            return $incident->load(['victims', 'perpetrators', 'evidence']);
        });
    }

    public function update($uid, array $data): bool
    {
        $incident = Incident::query()->where('uid', $uid)->first();
        return $incident->update($data);
    }

    public function delete(int $id): bool
    {
        $incident = Incident::findOrFail($id);
        return $incident->delete();
    }

    public function getByStatus(string $status): Collection
    {
        return Incident::query()->where('status', $status)->with(['victims', 'perpetrators'])->get();
    }

    public function getByType(string $type): Collection
    {
        return Incident::query()->where('type', $type)->with(['victims', 'perpetrators'])->get();
    }

    public function getIncidentByUid($uid)
    {
        return Incident::query()->where('uid', $uid)->with([
            'victims',
            'perpetrators',
            'evidence',
            'supportServices',
            'updates.user'
        ])->first();
    }

    public function getIncidentById($id)
    {
        return Incident::query()->where('id', $id)->with([
            'victims',
            'perpetrators',
            'evidence',
            'supportServices',
            'updates.user'
        ])->first();
    }


    public function getByReporter(int $reporterId): Collection
    {
        return Incident::query()->where('reporter_id', $reporterId)->with(['victims', 'perpetrators'])->get();
    }

    public function addCaseUpdate($incidentUid, array $updateData): void
    {
        $incident = $this->getIncidentByUid($incidentUid);
        $incident->updates()->create([
            'user_id' => auth()->id(),
            'update_text' => $updateData['update_text'],
            'status_change' => $updateData['status_change'] ?? null,
        ]);

        if (isset($updateData['status'])) {
            $incident->update(['status' => $updateData['status']]);
        }
    }

    public function attachSupportServices($incidentUid, array $serviceIds, array $pivotData = []): void
    {
        $incident = $this->getIncidentByUid($incidentUid);
        $incident->supportServices()->syncWithoutDetaching(
            collect($serviceIds)->mapWithKeys(function ($id) use ($pivotData) {
                return [
                    $id => ['notes' => $pivotData['notes'] ?? null, 'uid' =>  Str::uuid()]
                ];
            })->toArray()
        );
    }
}

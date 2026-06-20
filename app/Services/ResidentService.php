<?php

namespace App\Services;

use App\Models\Resident;
use App\Services\ActivityLogService;
use App\Services\CustomFieldService;
use Illuminate\Support\Facades\DB;

class ResidentService
{
    public function __construct(
        protected CustomFieldService $customFieldService,
        protected ActivityLogService $activityLogService,
    ) {}

    public function create(array $data, ?string $role = null): Resident
    {
        return DB::transaction(function () use ($data, $role) {
            $professionIds = $data['profession_category_ids'] ?? [];
            $customFields = $data['custom_fields'] ?? [];
            unset($data['profession_category_ids'], $data['custom_fields']);

            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $resident = Resident::create($data);
            $resident->professionCategories()->sync($professionIds);

            if (!empty($customFields)) {
                $this->customFieldService->syncValues($resident, $customFields, $role);
            }

            $this->activityLogService->log('created', $resident, $data);

            return $resident->load(['professionCategories', 'house.village', 'customFieldValues.definition']);
        });
    }

    public function update(Resident $resident, array $data, ?string $role = null): Resident
    {
        return DB::transaction(function () use ($resident, $data, $role) {
            $professionIds = $data['profession_category_ids'] ?? null;
            $customFields = $data['custom_fields'] ?? null;
            unset($data['profession_category_ids'], $data['custom_fields']);

            $data['updated_by'] = auth()->id();
            $old = $resident->toArray();

            $resident->update($data);

            if ($professionIds !== null) {
                $resident->professionCategories()->sync($professionIds);
            }

            if ($customFields !== null) {
                $this->customFieldService->syncValues($resident, $customFields, $role);
            }

            $this->activityLogService->log('updated', $resident, ['old' => $old, 'new' => $resident->fresh()->toArray()]);

            return $resident->load(['professionCategories', 'house.village', 'customFieldValues.definition']);
        });
    }

    public function verify(Resident $resident): Resident
    {
        $resident->update([
            'last_verified_at' => now(),
            'verified_by' => auth()->id(),
            'profile_status' => 'complete',
            'updated_by' => auth()->id(),
        ]);

        $this->activityLogService->log('verified', $resident);

        return $resident;
    }

    public function formOptions(): array
    {
        return [
            'enums' => \App\Enums\ResidentEnums::class,
            'genders' => \App\Enums\ResidentEnums::GENDERS,
            'blood_groups' => \App\Enums\ResidentEnums::BLOOD_GROUPS,
            'religions' => \App\Enums\ResidentEnums::RELIGIONS,
            'education_levels' => \App\Enums\ResidentEnums::EDUCATION_LEVELS,
            'marital_statuses' => \App\Enums\ResidentEnums::MARITAL_STATUSES,
            'resident_statuses' => \App\Enums\ResidentEnums::RESIDENT_STATUSES,
            'employment_sectors' => \App\Enums\ResidentEnums::EMPLOYMENT_SECTORS,
            'employment_statuses' => \App\Enums\ResidentEnums::EMPLOYMENT_STATUSES,
            'income_levels' => \App\Enums\ResidentEnums::INCOME_LEVELS,
            'zakat_statuses' => \App\Enums\ResidentEnums::ZAKAT_STATUSES,
            'aid_priorities' => \App\Enums\ResidentEnums::AID_PRIORITIES,
            'profile_statuses' => \App\Enums\ResidentEnums::PROFILE_STATUSES,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Resident;
use App\Services\ActivityLogService;
use App\Services\CustomFieldService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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

    public function filterParams(): array
    {
        return [
            'search', 'village_id', 'house_id', 'zakat_status', 'is_donation_giver_eligible',
            'is_donation_receiver_eligible', 'needs_urgent_aid', 'aid_priority',
            'employment_sector', 'income_level', 'is_widow', 'is_orphan',
            'has_disability', 'profession_category_id', 'preset',
            'profile_status', 'resident_status', 'gender',
        ];
    }

    public function buildFilteredQuery(Request $request, array $scope = []): Builder
    {
        $scope = array_merge([
            'active_only' => false,
            'complete_only' => false,
            'consent_only' => false,
        ], $scope);

        $query = Resident::with(['house.village', 'professionCategories']);

        if ($scope['active_only']) {
            $query->where('resident_status', 'active');
        }

        if ($scope['complete_only']) {
            $query->where('profile_status', 'complete');
        }

        if ($scope['consent_only']) {
            $query->where('consent_for_charity_contact', true);
        }

        $this->applyPreset($query, $request->preset);

        return $query
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name_bn', 'like', "%{$s}%")
                    ->orWhere('name_en', 'like', "%{$s}%")
                    ->orWhere('father_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('nid', 'like', "%{$s}%");
            }))
            ->when($request->village_id, fn ($q, $v) => $q->whereHas('house', fn ($h) => $h->where('village_id', $v)))
            ->when($request->house_id, fn ($q, $h) => $q->where('house_id', $h))
            ->when($request->filled('zakat_status'), fn ($q) => $q->where('zakat_status', $request->zakat_status))
            ->when($request->boolean('is_donation_giver_eligible'), fn ($q) => $q->where('is_donation_giver_eligible', true))
            ->when($request->boolean('is_donation_receiver_eligible'), fn ($q) => $q->where('is_donation_receiver_eligible', true))
            ->when($request->boolean('needs_urgent_aid'), fn ($q) => $q->where('needs_urgent_aid', true))
            ->when($request->aid_priority, fn ($q, $p) => $q->where('aid_priority', $p))
            ->when($request->employment_sector, fn ($q, $s) => $q->where('employment_sector', $s))
            ->when($request->income_level, fn ($q, $l) => $q->where('income_level', $l))
            ->when($request->boolean('is_widow'), fn ($q) => $q->where('is_widow', true))
            ->when($request->boolean('is_orphan'), fn ($q) => $q->where('is_orphan', true))
            ->when($request->boolean('has_disability'), fn ($q) => $q->where('has_disability', true))
            ->when($request->profession_category_id, fn ($q, $id) => $q->whereHas(
                'professionCategories',
                fn ($p) => $p->where('profession_categories.id', $id)
            ))
            ->when($request->filled('profile_status'), fn ($q) => $q->where('profile_status', $request->profile_status))
            ->when($request->filled('resident_status'), fn ($q) => $q->where('resident_status', $request->resident_status))
            ->when($request->filled('gender'), fn ($q) => $q->where('gender', $request->gender))
            ->latest('name_bn');
    }

    public function presets(): array
    {
        return [
            ['key' => 'zakat_payers', 'label_bn' => 'জাকাত দাতা', 'label_en' => 'Zakat Payers'],
            ['key' => 'zakat_receivers', 'label_bn' => 'জাকাত পাওয়ার উপযুক্ত', 'label_en' => 'Zakat Eligible'],
            ['key' => 'donation_givers', 'label_bn' => 'দান দাতা', 'label_en' => 'Donation Givers'],
            ['key' => 'donation_receivers', 'label_bn' => 'দান গ্রহীতা', 'label_en' => 'Donation Receivers'],
            ['key' => 'urgent_aid', 'label_bn' => 'জরুরি সহায়তা', 'label_en' => 'Urgent Aid'],
        ];
    }

    protected function applyPreset(Builder $query, ?string $preset): void
    {
        match ($preset) {
            'zakat_payers' => $query->where('zakat_status', 'pays'),
            'zakat_receivers' => $query->where('is_donation_receiver_eligible', true)
                ->whereIn('zakat_status', ['does_not_pay', 'not_applicable']),
            'donation_givers' => $query->where('is_donation_giver_eligible', true),
            'donation_receivers' => $query->where('is_donation_receiver_eligible', true),
            'urgent_aid' => $query->where('needs_urgent_aid', true)->where('is_aid_blacklisted', false),
            default => null,
        };
    }
}

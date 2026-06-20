<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import CustomFieldsForm from '@/Components/CustomFields/CustomFieldsForm.vue';

const props = defineProps({
    resident: Object,
    houses: Array,
    professionCategories: Array,
    customFieldDefinitions: Array,
    customFieldValues: Object,
    formOptions: Object,
    house: Object,
    isAdmin: { type: Boolean, default: false },
    submitRoute: String,
    method: { type: String, default: 'post' },
});

const { t, locale } = useI18n();
const tab = ref('personal');

const form = useForm({
    house_id: props.resident?.house_id ?? props.house?.id ?? '',
    name_bn: props.resident?.name_bn ?? '',
    name_en: props.resident?.name_en ?? '',
    father_name: props.resident?.father_name ?? '',
    gender: props.resident?.gender ?? 'male',
    date_of_birth: props.resident?.date_of_birth?.substring?.(0, 10) ?? '',
    phone: props.resident?.phone ?? '',
    nid: props.resident?.nid ?? '',
    blood_group: props.resident?.blood_group ?? '',
    religion: props.resident?.religion ?? '',
    education_level: props.resident?.education_level ?? '',
    marital_status: props.resident?.marital_status ?? '',
    resident_status: props.resident?.resident_status ?? 'active',
    household_head: props.resident?.household_head ?? false,
    dependents_count: props.resident?.dependents_count ?? 0,
    employment_sector: props.resident?.employment_sector ?? '',
    employment_status: props.resident?.employment_status ?? '',
    organization_name: props.resident?.organization_name ?? '',
    designation: props.resident?.designation ?? '',
    monthly_income: props.resident?.monthly_income ?? '',
    income_level: props.resident?.income_level ?? '',
    is_donation_giver_eligible: props.resident?.is_donation_giver_eligible ?? false,
    is_donation_receiver_eligible: props.resident?.is_donation_receiver_eligible ?? false,
    zakat_status: props.resident?.zakat_status ?? 'not_applicable',
    is_probashi: props.resident?.is_probashi ?? false,
    migration_country: props.resident?.migration_country ?? '',
    has_disability: props.resident?.has_disability ?? false,
    disability_type: props.resident?.disability_type ?? '',
    is_widow: props.resident?.is_widow ?? false,
    is_orphan: props.resident?.is_orphan ?? false,
    needs_urgent_aid: props.resident?.needs_urgent_aid ?? false,
    aid_priority: props.resident?.aid_priority ?? 'normal',
    is_aid_blacklisted: props.resident?.is_aid_blacklisted ?? false,
    blacklist_reason: props.resident?.blacklist_reason ?? '',
    consent_for_charity_contact: props.resident?.consent_for_charity_contact ?? true,
    profile_status: props.resident?.profile_status ?? 'draft',
    notes: props.resident?.notes ?? '',
    profession_category_ids: props.resident?.profession_categories?.map(p => p.id) ?? [],
    custom_fields: props.customFieldValues ?? {},
});

const tabs = [
    { id: 'personal', label: 'personal_info' },
    { id: 'household', label: 'household_info' },
    { id: 'employment', label: 'employment_info' },
    { id: 'charity', label: 'charity_info' },
    { id: 'vulnerability', label: 'vulnerability_info' },
    { id: 'custom', label: 'custom_info' },
];

const submit = () => {
    if (props.method === 'put') {
        form.put(props.submitRoute);
    } else {
        form.post(props.submitRoute);
    }
};

const profLabel = (p) => locale.value === 'bn' ? p.name_bn : p.name_en;
const customDefsForSection = (section) => props.customFieldDefinitions?.filter(d => d.form_section === section) ?? [];
const customDefsCustom = () => props.customFieldDefinitions?.filter(d => d.form_section === 'custom') ?? [];
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-4">
            <button v-for="tb in tabs" :key="tb.id" type="button" @click="tab = tb.id"
                :class="['px-4 py-2 text-sm font-medium rounded-lg transition', tab === tb.id ? 'bg-primary-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']">
                {{ t(tb.label) }}
            </button>
        </div>

        <div v-show="tab === 'personal'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-if="isAdmin">
                <label class="block text-sm font-medium text-slate-700 mb-1">House</label>
                <select v-model="form.house_id" class="w-full rounded-lg border-slate-300">
                    <option v-for="h in houses" :key="h.id" :value="h.id">{{ h.house_name }} ({{ h.village?.name_bn }})</option>
                </select>
            </div>
            <div v-else-if="house" class="md:col-span-2 p-3 bg-primary-50 rounded-lg text-sm text-primary-800">
                {{ house.house_name }} — {{ house.village?.name_bn }}
            </div>
            <div><label class="block text-sm font-medium mb-1">{{ t('name_bn') }}</label><input v-model="form.name_bn" class="w-full rounded-lg border-slate-300" required /></div>
            <div><label class="block text-sm font-medium mb-1">{{ t('name_en') }}</label><input v-model="form.name_en" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">Father's Name</label><input v-model="form.father_name" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">Gender</label>
                <select v-model="form.gender" class="w-full rounded-lg border-slate-300"><option v-for="g in formOptions.genders" :key="g" :value="g">{{ g }}</option></select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Date of Birth</label><input type="date" v-model="form.date_of_birth" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">Phone</label><input v-model="form.phone" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">NID</label><input v-model="form.nid" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">Blood Group</label>
                <select v-model="form.blood_group" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="b in formOptions.blood_groups" :key="b" :value="b">{{ b }}</option></select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Religion</label>
                <select v-model="form.religion" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="r in formOptions.religions" :key="r" :value="r">{{ r }}</option></select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Education</label>
                <select v-model="form.education_level" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="e in formOptions.education_levels" :key="e" :value="e">{{ e }}</option></select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Marital Status</label>
                <select v-model="form.marital_status" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="m in formOptions.marital_statuses" :key="m" :value="m">{{ m }}</option></select>
            </div>
            <CustomFieldsForm :definitions="customDefsForSection('personal')" v-model="form.custom_fields" :errors="form.errors" />
        </div>

        <div v-show="tab === 'household'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.household_head" class="rounded text-primary-600" /> Household Head</label>
            <div><label class="block text-sm font-medium mb-1">Dependents</label><input type="number" v-model="form.dependents_count" min="0" class="w-full rounded-lg border-slate-300" /></div>
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.consent_for_charity_contact" class="rounded text-primary-600" /> Consent for charity contact</label>
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_probashi" class="rounded text-primary-600" /> Probashi</label>
            <div v-if="form.is_probashi"><label class="block text-sm font-medium mb-1">Migration Country</label><input v-model="form.migration_country" class="w-full rounded-lg border-slate-300" /></div>
            <CustomFieldsForm :definitions="customDefsForSection('household')" v-model="form.custom_fields" :errors="form.errors" />
        </div>

        <div v-show="tab === 'employment'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Professions</label>
                <div class="flex flex-wrap gap-2">
                    <label v-for="p in professionCategories" :key="p.id" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 rounded-lg text-sm">
                        <input type="checkbox" :value="p.id" v-model="form.profession_category_ids" class="rounded text-primary-600" /> {{ profLabel(p) }}
                    </label>
                </div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Employment Sector</label>
                <select v-model="form.employment_sector" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="s in formOptions.employment_sectors" :key="s" :value="s">{{ s }}</option></select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Employment Status</label>
                <select v-model="form.employment_status" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="s in formOptions.employment_statuses" :key="s" :value="s">{{ s }}</option></select>
            </div>
            <div><label class="block text-sm font-medium mb-1">Organization</label><input v-model="form.organization_name" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">Designation</label><input v-model="form.designation" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">Monthly Income (BDT)</label><input type="number" v-model="form.monthly_income" class="w-full rounded-lg border-slate-300" /></div>
            <div><label class="block text-sm font-medium mb-1">Income Level</label>
                <select v-model="form.income_level" class="w-full rounded-lg border-slate-300"><option value="">—</option><option v-for="i in formOptions.income_levels" :key="i" :value="i">{{ i }}</option></select>
            </div>
            <CustomFieldsForm :definitions="customDefsForSection('employment')" v-model="form.custom_fields" :errors="form.errors" />
        </div>

        <div v-show="tab === 'charity'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_donation_giver_eligible" class="rounded text-primary-600" /> Donation Giver Eligible</label>
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_donation_receiver_eligible" class="rounded text-primary-600" /> Donation Receiver Eligible</label>
            <div><label class="block text-sm font-medium mb-1">Zakat Status</label>
                <select v-model="form.zakat_status" class="w-full rounded-lg border-slate-300"><option v-for="z in formOptions.zakat_statuses" :key="z" :value="z">{{ z }}</option></select>
            </div>
            <div v-if="isAdmin"><label class="block text-sm font-medium mb-1">Profile Status</label>
                <select v-model="form.profile_status" class="w-full rounded-lg border-slate-300"><option v-for="p in formOptions.profile_statuses" :key="p" :value="p">{{ p }}</option></select>
            </div>
            <CustomFieldsForm :definitions="customDefsForSection('charity')" v-model="form.custom_fields" :errors="form.errors" />
        </div>

        <div v-show="tab === 'vulnerability'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.has_disability" class="rounded text-primary-600" /> Has Disability</label>
            <div v-if="form.has_disability"><input v-model="form.disability_type" placeholder="Disability type" class="w-full rounded-lg border-slate-300" /></div>
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_widow" class="rounded text-primary-600" /> Widow</label>
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_orphan" class="rounded text-primary-600" /> Orphan</label>
            <label class="flex items-center gap-2"><input type="checkbox" v-model="form.needs_urgent_aid" class="rounded text-primary-600" /> Needs Urgent Aid</label>
            <div><label class="block text-sm font-medium mb-1">Aid Priority</label>
                <select v-model="form.aid_priority" class="w-full rounded-lg border-slate-300"><option v-for="a in formOptions.aid_priorities" :key="a" :value="a">{{ a }}</option></select>
            </div>
            <template v-if="isAdmin">
                <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_aid_blacklisted" class="rounded text-red-600" /> Aid Blacklisted</label>
                <div v-if="form.is_aid_blacklisted"><input v-model="form.blacklist_reason" placeholder="Reason" class="w-full rounded-lg border-slate-300" /></div>
            </template>
            <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Notes</label><textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-slate-300" /></div>
            <CustomFieldsForm :definitions="customDefsForSection('vulnerability')" v-model="form.custom_fields" :errors="form.errors" />
        </div>

        <div v-show="tab === 'custom'">
            <CustomFieldsForm :definitions="customDefsCustom()" v-model="form.custom_fields" :errors="form.errors" />
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-primary-700 text-white rounded-lg font-medium hover:bg-primary-800 disabled:opacity-50">{{ t('save') }}</button>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from 'chart.js';
import { useI18n } from 'vue-i18n';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    charts: { type: Array, default: () => [] },
});

const { locale, t } = useI18n();

const chartItems = computed(() => props.charts.map((chart) => {
    const labels = locale.value === 'bn' ? chart.labels_bn : chart.labels_en;
    const title = locale.value === 'bn' ? chart.title_bn : chart.title_en;

    return {
        key: chart.key,
        title,
        data: {
            labels,
            datasets: [{
                label: t('population'),
                data: chart.values,
                backgroundColor: chart.colors,
                borderRadius: 6,
                maxBarThickness: 48,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.label}: ${ctx.parsed.y}`,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: '#F1F5F9' },
                },
                x: {
                    grid: { display: false },
                    ticks: { maxRotation: 45, minRotation: 0, font: { size: 11 } },
                },
            },
        },
    };
}));
</script>

<template>
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div v-for="chart in chartItems" :key="chart.key" class="bg-white rounded-xl border p-4">
            <h3 class="font-semibold text-slate-800 mb-3 text-sm">{{ chart.title }}</h3>
            <div class="h-56">
                <Bar :data="chart.data" :options="chart.options" />
            </div>
        </div>
    </div>
</template>

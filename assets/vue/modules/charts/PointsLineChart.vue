<script setup>
import { onMounted, ref, watch } from 'vue'
import VChart from 'vue-echarts'
import PointsLineChartLegend from '@/modules/charts/PointsLineChartLegend.vue'
import * as echarts from 'echarts/core'
import { LineChart } from 'echarts/charts'
import { GridComponent, TooltipComponent, LegendComponent, DataZoomComponent } from 'echarts/components'
import { CanvasRenderer } from 'echarts/renderers'
import { usePointsLineChartData } from '@/modules/charts/composables/pointsLineChart/usePointsLineChartData.js'
import { usePointsLineChartNavigation } from '@/modules/charts/composables/pointsLineChart/usePointsLineChartNavigation.js'
import { usePointsLineChartOptions } from '@/modules/charts/composables/pointsLineChart/usePointsLineChartOptions.js'
import { usePointsLineChartHighlight } from '@/modules/charts/composables/pointsLineChart/usePointsLineChartHighlight.js'
import AppLoader from '@/components/loaders/AppLoader.vue'
import { useI18n } from 'vue-i18n'
import AppNoData from '@/components/no_data/AppNoData.vue'

echarts.use([LineChart, GridComponent, TooltipComponent, LegendComponent, DataZoomComponent, CanvasRenderer])

const { t } = useI18n()
const chartRef = ref()
const { data, loading, users, chartKeys, loadData } = usePointsLineChartData()
const { currentKey, currentChart, goToNextChart, goToPreviousChart } = usePointsLineChartNavigation(data, chartKeys)
const visibleUsers = ref({})

watch(
    users,
    (list) => {
        list.forEach((u) => {
            visibleUsers.value[u.userId] = true
        })
    },
    { immediate: true },
)

const { chartOption } = usePointsLineChartOptions(currentKey, currentChart, visibleUsers)
const { highlightUser, downplayUser } = usePointsLineChartHighlight(chartRef)

const onClick = (p) => {
    if (data.value[p.value]) {
        currentKey.value = p.value
    }
}

onMounted(loadData)
</script>

<template>
    <div class="card">
        <AppLoader v-if="loading" />
        <template v-else-if="chartKeys.length > 0">
            <template v-if="currentKey !== 'master'">
                <div class="d-flex justify-content-center subcharts-menu pt-2 p-lg-3">
                    <button class="btn mb-3" @click="goToPreviousChart()">
                        {{ t('charts.pointsLine.subchartsMenu.before') }}
                    </button>
                    <button class="btn mb-3 mx-3" @click="currentKey = 'master'">
                        {{ t('charts.pointsLine.subchartsMenu.main') }}
                    </button>
                    <button class="btn mb-3" @click="goToNextChart()">
                        {{ t('charts.pointsLine.subchartsMenu.next') }}
                    </button>
                </div>

                <h4 class="text-center">{{ currentKey }}</h4>
            </template>
            <VChart v-if="!loading" ref="chartRef" class="chart" :option="chartOption" autoresize @click="onClick" />

            <PointsLineChartLegend
                v-model="visibleUsers"
                :users="users"
                @highlight="highlightUser"
                @downplay="downplayUser"
            />
        </template>
        <AppNoData v-else />
    </div>
</template>

<style scoped>
.chart {
    height: 600px;
    width: 100%;

    @media (max-height: 600px) {
        height: calc(100vh - 62px);
    }
}

.subcharts-menu {
    button {
        background: rgb(var(--primary-rgb), 0.12) !important;
        color: var(--primary) !important;
    }
}
</style>

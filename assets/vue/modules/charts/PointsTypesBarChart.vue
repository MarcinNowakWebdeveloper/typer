<script setup>
import * as echarts from 'echarts/core'
import VChart from 'vue-echarts'
import { CanvasRenderer } from 'echarts/renderers'
import { BarChart, LineChart } from 'echarts/charts'
import { GridComponent, TooltipComponent, LegendComponent, DataZoomComponent } from 'echarts/components'
import { usePointsTypesBarChartData } from '@/modules/charts/composables/pointsTypesBarChart/usePointsTypesBarChartData.js'
import { usePointsTypesBarChartOptions } from '@/modules/charts/composables/pointsTypesBarChart/usePointsTypesBarChartOptions.js'
import { onMounted, ref, watch } from 'vue'
import PointsTypesBarChartXLegend from '@/modules/charts/PointsTypesBarChartXLegend.vue'
import PointsTypesBarChartYLegend from '@/modules/charts/PointsTypesBarChartYLegend.vue'
import AppLoader from '@/components/loaders/AppLoader.vue'
import AppNoData from '@/components/no_data/AppNoData.vue'

echarts.use([CanvasRenderer, LineChart, BarChart, GridComponent, TooltipComponent, LegendComponent, DataZoomComponent])

const { loading, sortedUsers, pointTypes, loadData } = usePointsTypesBarChartData()

const visibleTypes = ref({})

watch(
    pointTypes,
    (list) => {
        list.forEach((u) => {
            visibleTypes.value[u.key] = true
        })
    },
    { immediate: true },
)

const { chartOption } = usePointsTypesBarChartOptions(visibleTypes, pointTypes, sortedUsers)

const updateVisibleTypes = (newVisibleTypes) => {
    visibleTypes.value = newVisibleTypes
}

onMounted(loadData)
</script>
<template>
    <div class="card">
        <AppLoader v-if="loading" />
        <template v-else-if="sortedUsers.length > 0">
            <PointsTypesBarChartXLegend
                :point-types="pointTypes"
                :visible-types="visibleTypes"
                @update:visible-types="updateVisibleTypes"
            />
            <div class="chart-wrapper d-flex">
                <PointsTypesBarChartYLegend :users="sortedUsers" />
                <VChart class="chart" :option="chartOption" autoresize />
            </div>
        </template>
        <AppNoData v-else />
    </div>
</template>

<style>
.chart {
    height: 600px;
    width: calc(100% - 232px);
}
</style>

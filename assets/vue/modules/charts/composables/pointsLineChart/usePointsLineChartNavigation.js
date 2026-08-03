import { computed, ref } from 'vue'

export function usePointsLineChartNavigation(data, chartKeys) {
    const currentKey = ref('master')

    const currentChart = computed(() => data.value[currentKey.value])

    const currentChartIndex = computed(() => {
        if (currentKey.value === 'master') {
            return -1
        }

        return chartKeys.value.indexOf(currentKey.value)
    })

    const goToNextChart = () => {
        if (currentKey.value === 'master' && chartKeys.value.length) {
            currentKey.value = chartKeys.value[0]
            return
        }

        if (currentChartIndex.value < chartKeys.value.length - 1) {
            currentKey.value = chartKeys.value[currentChartIndex.value + 1]
        }
    }

    const goToPreviousChart = () => {
        if (currentChartIndex.value > 0) {
            currentKey.value = chartKeys.value[currentChartIndex.value - 1]
            return
        }

        if (currentChartIndex.value === 0) {
            currentKey.value = 'master'
        }
    }

    return {
        currentKey,
        currentChart,
        goToNextChart,
        goToPreviousChart,
    }
}

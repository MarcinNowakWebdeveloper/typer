import { computed, ref } from 'vue'
import api from '@/services/api.js'
import { useSettingsStore } from '@/stores/settings.js'
import { useI18n } from 'vue-i18n'

export function usePointsLineChartData() {
    const { t } = useI18n()
    const chartData = ref({})
    const loading = ref(true)

    const loadData = async () => {
        let url = '/api/charts/points_line'
        let settings = useSettingsStore()
        if (settings.pointCountingStrategies) {
            url += '?pointCountingStrategies=' + settings.pointCountingStrategies
        }

        try {
            const { data } = await api.get(url)
            chartData.value = data
        } catch (e) {
            alert(e.response?.data?.message ?? t('common.errors.500'))
        } finally {
            loading.value = false
        }
    }

    const users = computed(() => chartData.value.master?.series ?? [])

    const chartKeys = computed(() => Object.keys(chartData.value).filter((key) => key !== 'master'))

    return {
        data: chartData,
        loading,
        users,
        chartKeys,
        loadData,
    }
}

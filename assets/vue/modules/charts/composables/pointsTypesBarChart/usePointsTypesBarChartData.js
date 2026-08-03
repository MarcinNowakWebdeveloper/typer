import { computed, ref } from 'vue'
import api from '@/services/api'
import { useSettingsStore } from '@/stores/settings'
import { useI18n } from 'vue-i18n'

export function usePointsTypesBarChartData() {
    const { t } = useI18n()
    const charData = ref({})
    const loading = ref(true)

    const loadData = async () => {
        let url = '/api/charts/points_types_bar'
        let settings = useSettingsStore()
        if (settings.pointCountingStrategies) {
            url += '?pointCountingStrategies=' + settings.pointCountingStrategies
        }

        try {
            const { data } = await api.get(url)
            charData.value = data
        } catch (e) {
            alert(e.response?.data?.message ?? t('common.errors.500'))
        } finally {
            loading.value = false
        }
    }

    const sortedUsers = computed(() => {
        return Object.values(charData.value.users ?? {}).sort((a, b) => {
            const sumA = Object.values(a.types).reduce((sum, value) => sum + value, 0)
            const sumB = Object.values(b.types).reduce((sum, value) => sum + value, 0)

            return sumA - sumB
        })
    })

    const pointTypes = computed(() => charData.value.pointTypes ?? [])

    return {
        loading,
        sortedUsers,
        pointTypes,
        loadData,
    }
}

import { ref } from 'vue'
import api from '@/services/api.js'
import { useI18n } from 'vue-i18n'

export function useGamesList(id) {
    const loading = ref(true)
    const stageGroup = ref({})
    const { t } = useI18n()

    const loadGames = async () => {
        try {
            loading.value = true

            const { data } = await api.get(`/api/admin/config/stage/group:${id}`)

            stageGroup.value = data
        } catch (e) {
            alert(e.response?.data?.message ?? t('common.errors.500'))
        } finally {
            loading.value = false
        }
    }

    return {
        loading,
        stageGroup,
        loadGames,
    }
}

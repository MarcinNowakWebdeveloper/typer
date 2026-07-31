import { ref } from 'vue'
import api from '@/services/api'
import { useI18n } from 'vue-i18n'

export function usePrediction(gameId, homeGoals = null, awayGoals = null, showSuccess) {
    const { t } = useI18n()

    const loading = ref(false)

    const submit = async () => {
        try {
            loading.value = true
            await api.post(`/api/user/game/edit:${gameId}`, {
                homeGoals: homeGoals,
                awayGoals: awayGoals,
            })

            showSuccess()
        } catch (e) {
            alert(e.response?.data?.message ?? t('common.errors.500'))
        } finally {
            loading.value = false
        }
    }

    return {
        loading,
        homeGoals,
        awayGoals,
        submit,
    }
}

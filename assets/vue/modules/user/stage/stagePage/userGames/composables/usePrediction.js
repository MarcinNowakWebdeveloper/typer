import { ref } from 'vue'
import api from '@/services/api'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast.js'

export function usePrediction(gameId, homeGoals = null, awayGoals = null, showSuccess) {
    const { t } = useI18n()

    const loading = ref(false)

    const submit = async () => {
        try {
            loading.value = true
            const { data } = await api.post(`/api/user/game/edit:${gameId}`, {
                homeGoals: homeGoals,
                awayGoals: awayGoals,
            })

            if (data.success === true) {
                showSuccess()
            } else {
                let message = data.message ? data.message : t('common.errors.500')
                toast.error(message)
            }
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
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

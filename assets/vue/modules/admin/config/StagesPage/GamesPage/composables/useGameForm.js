import { ref } from 'vue'
import api from '@/services/api.js'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast.js'

export function useGameForm(props, emit) {
    const { t } = useI18n()
    const loading = ref(false)

    const form = ref({
        homeTeam: props.game?.homeTeam.id,
        awayTeam: props.game?.awayTeam.id,
        homeGoals: props.game?.homeGoals,
        awayGoals: props.game?.awayGoals,
        date: props.game?.date,
        time: props.game?.time,
    })

    const submit = async () => {
        try {
            loading.value = true

            const payload = {
                stageGroupId: props.stageGroup.id,
                homeTeamId: form.value.homeTeam,
                awayTeamId: form.value.awayTeam,
                homeGoals: form.value.homeGoals,
                awayGoals: form.value.awayGoals,
                date: form.value.date,
                time: form.value.time,
            }

            if (props.game?.id) {
                const { data } = await api.patch(`/api/admin/config/game:${props.game.id}/edit`, payload)
                if (data.success !== true) {
                    let message = data.message ? data.message : t('common.errors.500')
                    toast.error(message)
                }
            } else {
                const { data } = await api.post('/api/admin/config/game/add', payload)
                if (data.success === true) {
                    resetForm()
                } else {
                    let message = data.message ? data.message : t('common.errors.500')
                    toast.error(message)
                }
            }

            emit('saved', props.game?.id)
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        } finally {
            loading.value = false
        }
    }

    const resetForm = () => {
        form.value.homeTeam = null
        form.value.awayTeam = null
        form.value.homeGoals = null
        form.value.awayGoals = null
    }

    const close = () => {
        emit('close', props.game?.id)
    }

    return {
        loading,
        form,
        submit,
        close,
    }
}

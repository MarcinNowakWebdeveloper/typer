import { ref } from 'vue'
import api from '@/services/api.js'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast.js'

export function useGroupForm(props, emit, selectedTeamIds, selectedTeams, teamSearch) {
    const { t } = useI18n()
    const loading = ref(false)
    const form = ref({
        name: props.name,
    })

    const submit = async () => {
        try {
            loading.value = true

            const payload = {
                name: form.value.name,
                teamIds: [...selectedTeamIds.value],
            }

            if (props.id) {
                const { data } = await api.patch(`/api/admin/config/group:${props.id}/edit`, payload)
                if (data.success !== true) {
                    let message = data.message ? data.message : t('common.errors.500')
                    toast.error(message)
                }
            } else {
                const { data } = await api.post('/api/admin/config/group/add', payload)
                if (data.success === true) {
                    resetForm()
                } else {
                    let message = data.message ? data.message : t('common.errors.500')
                    toast.error(message)
                }
            }

            emit('saved', props.id)
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        } finally {
            loading.value = false
        }
    }

    const resetForm = () => {
        form.value.name = ''
        selectedTeams.value = []
        teamSearch.value = ''
    }

    const close = () => {
        emit('close', props.id)
    }

    return {
        form,
        loading,
        submit,
        close,
    }
}

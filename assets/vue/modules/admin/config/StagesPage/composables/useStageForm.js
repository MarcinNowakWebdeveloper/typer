import { useI18n } from 'vue-i18n'
import api from '@/services/api.js'
import { ref } from 'vue'
import toast from '@/services/toast.js'

export function useStageForm(props, emit, selectedGroupIds, selectedGroups, groupSearch) {
    const { t } = useI18n()
    const loading = ref(false)
    const form = ref({
        name: props.name,
        shortName: props.shortName,
    })

    const submit = async () => {
        try {
            loading.value = true

            const payload = {
                name: form.value.name,
                groupIds: [...selectedGroupIds.value],
            }

            if (props.id) {
                const { data } = await api.patch(`/api/admin/config/stage:${props.id}/edit`, payload)
                if (data.success !== true) {
                    let message = data.message ? data.message : t('common.errors.500')
                    toast.error(message)
                }
            } else {
                const { data } = await api.post('/api/admin/config/stage/add', payload)
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
        selectedGroups.value = []
        groupSearch.value = ''
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

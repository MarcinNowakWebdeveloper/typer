import { ref } from 'vue'
import api from '@/services/api.js'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast.js'

export function useTeamForm(props, emit, image) {
    const loading = ref(false)
    const { t } = useI18n()

    const form = ref({
        name: props.name,
    })

    const submit = async () => {
        loading.value = true

        const payload = new FormData()

        payload.append('name', form.value.name)

        if (image.value) {
            payload.append('image', image.value)
        }

        try {
            if (props.id) {
                const { data } = await api.patch(`/api/admin/config/team:${props.id}/edit`, payload, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                if (data.success !== true) {
                    let message = data.message ? data.message : t('common.errors.500')
                    toast.error(message)
                }
            } else {
                const { data } = await api.post('/api/admin/config/team/add', payload, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
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
        image.value = null
    }

    const close = () => emit('close', props.id)

    return {
        form,
        loading,
        submit,
        close,
    }
}

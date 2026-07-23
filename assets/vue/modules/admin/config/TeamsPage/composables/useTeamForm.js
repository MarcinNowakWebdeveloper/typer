import { ref } from 'vue'
import api from '@/services/api.js'
import { useI18n } from 'vue-i18n'

export function useTeamForm(props, emit, image) {
    const loading = ref(false)
    const { t } = useI18n()

    const form = ref({
        name: props.name,
    })

    const submit = async () => {
        loading.value = true

        const data = new FormData()

        data.append('name', form.value.name)

        if (image.value) {
            data.append('image', image.value)
        }

        try {
            let url = '/api/admin/config/team/add'
            if (props.id) {
                url = `/api/admin/config/team:${props.id}/edit`
            }

            await api.post(url, data, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })

            if (!props.id) {
                resetForm()
            }

            emit('saved', props.id)
        } catch (e) {
            alert(e.response?.data?.message ?? t('common.errors.500'))
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

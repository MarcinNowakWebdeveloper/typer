import { ref } from 'vue'
import toast from '@/services/toast'
import api from '@/services/api.js'
import { useI18n } from 'vue-i18n'

export function useRegistryForm() {
    const { t } = useI18n()
    const loading = ref(false)

    const form = ref({
        email: '',
        password: '',
        name: '',
    })

    const resetForm = () => {
        form.value.email = ''
        form.value.password = ''
        form.value.name = ''
    }

    const register = async () => {
        try {
            loading.value = true
            const payload = {
                email: form.value.email,
                password: form.value.password,
                name: form.value.name,
            }

            const { data } = await api.post('/api/register', payload)
            if (data.success === true) {
                toast.success(t('auth.registerPage.response.success'))
                resetForm()
            } else {
                let message = data.message ? data.message : t('common.errors.500')
                toast.error(message)
            }
        } catch (e) {
            toast.error(t('common.errors.500'))
            console.log(e)
        } finally {
            loading.value = false
        }
    }

    return {
        form,
        loading,
        register,
    }
}

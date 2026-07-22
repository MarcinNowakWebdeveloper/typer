import api from '@/services/api.js'
import toast from '@/services/toast.js'
import { useI18n } from 'vue-i18n'

export function useUserAdministration(loadUsers, loadStats) {
    const { t } = useI18n()

    const activate = async (userId) => {
        try {
            const { data } = await api.post(`/api/admin/users/${userId}/activate`)
            if (data.success === true) {
                await Promise.all([loadUsers(), loadStats()])
            } else {
                let message = data.message ? data.message : t('common.errors.500')
                toast.error(message)
            }
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        }
    }

    const deactivate = async (userId) => {
        try {
            const { data } = await api.post(`/api/admin/users/${userId}/deactivate`)
            if (data.success === true) {
                await Promise.all([loadUsers(), loadStats()])
            } else {
                let message = data.message ? data.message : t('common.errors.500')
                toast.error(message)
            }
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        }
    }

    return {
        activate,
        deactivate,
    }
}

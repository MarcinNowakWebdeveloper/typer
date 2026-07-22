import { ref } from 'vue'
import api from '@/services/api.js'
import toast from '@/services/toast.js'
import { useI18n } from 'vue-i18n'

export function useUserStats() {
    const { t } = useI18n()
    const stats = ref(null)

    const loadStats = async () => {
        try {
            const { data } = await api.get('/api/admin/users/stats')
            stats.value = data
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        }
    }

    return {
        stats,
        loadStats,
    }
}

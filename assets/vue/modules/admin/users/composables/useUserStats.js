import { ref } from 'vue'
import api from '@/services/api.js'

export function useUserStats() {
    const stats = ref(null)

    const loadStats = async () => {
        const response = await api.get('/api/admin/users/stats')

        stats.value = response.data
    }

    return {
        stats,
        loadStats,
    }
}

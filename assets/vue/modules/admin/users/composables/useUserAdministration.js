import api from '@/services/api.js'

export function useUserAdministration(loadUsers, loadStats) {
    const activate = async (userId) => {
        await api.post(`/api/admin/users/${userId}/activate`)

        await Promise.all([loadUsers(), loadStats()])
    }

    const deactivate = async (userId) => {
        await api.post(`/api/admin/users/${userId}/deactivate`)

        await Promise.all([loadUsers(), loadStats()])
    }

    return {
        activate,
        deactivate,
    }
}

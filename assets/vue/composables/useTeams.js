import api from '@/services/api.js'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

export function useTeams() {
    const teams = ref([])
    const teamsLoading = ref(true)
    const { t } = useI18n()

    const loadTeams = async () => {
        try {
            teamsLoading.value = true
            const { data } = await api.get('/api/team/list')
            teams.value = data
        } catch (e) {
            alert(e.response?.data?.message ?? t('common.errors.500'))
        } finally {
            teamsLoading.value = false
        }
    }

    return {
        loadTeams,
        teams,
        teamsLoading,
    }
}

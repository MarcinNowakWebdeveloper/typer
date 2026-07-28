import { useI18n } from 'vue-i18n'
import api from '@/services/api.js'
import { ref } from 'vue'
import toast from '@/services/toast.js'

export function useGamesForm(loadGames) {
    const { t } = useI18n()
    const editingIds = ref(new Set())
    const edit = (gameId) => {
        if (!editingIds.value.has(gameId)) {
            editingIds.value.add(gameId)
        }
    }

    const edited = (gameId) => {
        loadGames()
        close(gameId)
    }

    const remove = async (gameId) => {
        try {
            await api.delete(`/api/admin/config/game:${gameId}/remove`)

            await loadGames()
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        }
    }

    const close = (gameId) => {
        editingIds.value.delete(gameId)
    }
    return {
        editingIds,
        edit,
        edited,
        remove,
        close,
    }
}

import { ref } from 'vue'
import api from '@/services/api'

export function useStagePredictions() {
    const predictions = ref({})

    const loadPredictions = async (gamesIds) => {
        predictions.value = {}

        if (!gamesIds.length) {
            return
        }

        const { data } = await api.post('/api/user/games', {
            gamesIds: gamesIds,
        })

        data.forEach((game) => {
            predictions.value[game.id] = {
                homeGoals: game.homeGoals,
                awayGoals: game.awayGoals,
            }
        })
    }

    return {
        predictions,
        loadPredictions,
    }
}

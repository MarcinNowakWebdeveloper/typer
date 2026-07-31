import { ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

export function useStage() {
    const route = useRoute()

    const stage = ref(null)
    const stages = ref(null)
    const showFutureGames = ref(true)

    const loadStage = async () => {
        const url = route.params.id ? `/api/stage:${route.params.id}` : '/api/stage'

        const { data } = await api.get(url)

        stage.value = data.stage
        stages.value = data.stages
        showFutureGames.value = Object.values(stage.value?.games.future ?? {}).length > 0
    }

    return {
        stage,
        stages,
        showFutureGames,
        loadStage,
    }
}

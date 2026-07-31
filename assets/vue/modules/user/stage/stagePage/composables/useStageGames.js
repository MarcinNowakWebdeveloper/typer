import { computed } from 'vue'

export function useStageGames(stage) {
    const futureGames = computed(() => Object.values(stage.value?.games.future ?? {}))

    const pastGames = computed(() => Object.values(stage.value?.games.past ?? {}))

    const gamesIds = computed(() => [...futureGames.value, ...pastGames.value].map((game) => game.id))

    const hasFutureGames = computed(() => futureGames.value.length > 0)
    const hasPastGames = computed(() => pastGames.value.length > 0)

    const showTypeChooser = computed(() => hasFutureGames.value && hasPastGames.value)

    return {
        futureGames,
        pastGames,
        gamesIds,
        hasFutureGames,
        hasPastGames,
        showTypeChooser,
    }
}

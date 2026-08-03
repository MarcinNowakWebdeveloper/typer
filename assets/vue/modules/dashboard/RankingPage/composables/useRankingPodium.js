import { computed } from 'vue'

export function useRankingPodium(ranking) {
    const podiumData = computed(() => {
        if (ranking.length < 3) {
            return []
        }

        return ranking
            .slice(0, 3)
            .sort((a, b) => b.points - a.points)
            .map((item, index) => ({
                userName: item.user.name,
                userColor: item.user.color,
                points: item.points,
                color: ['gold', 'silver', 'bronze'][index],
                position: index + 1,
            }))
    })

    const sorted = computed(() => [...podiumData.value].sort((a, b) => b.points - a.points))

    const first = computed(() => sorted.value?.[0] ?? null)
    const second = computed(() => sorted.value[1] ?? null)
    const third = computed(() => sorted.value[2] ?? null)

    return {
        first,
        second,
        third,
    }
}

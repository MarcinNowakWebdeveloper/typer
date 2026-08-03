import { computed, ref } from 'vue'

export function useRankingTable(ranking) {
    const sortColumn = ref('position')
    const sortDirection = ref('asc')

    const sortedRanking = computed(() => {
        if (!ranking) return null

        const data = [...ranking]
        return data.sort((a, b) => {
            let aVal, bVal
            switch (sortColumn.value) {
                case 'position':
                    aVal = a.position
                    bVal = b.position
                    break
                case 'changeOfPosition':
                    aVal = a.changeOfPosition
                    bVal = b.changeOfPosition
                    break
                case 'todayPoints':
                    aVal = a.todayPoints
                    bVal = b.todayPoints
                    break
                default:
                    return 0
            }

            if (sortDirection.value === 'asc') {
                return aVal - bVal
            } else {
                return bVal - aVal
            }
        })
    })

    const handleSort = (column) => {
        if (sortColumn.value === column) {
            sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
        } else {
            sortColumn.value = column
            sortDirection.value = 'asc'
        }
    }

    return {
        sortedRanking,
        handleSort,
        sortDirection,
        sortColumn,
    }
}

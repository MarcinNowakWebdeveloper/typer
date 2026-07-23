import { computed, ref } from 'vue'

export function useGroupTeams(groupTeams, teams) {
    const selectedTeams = ref([...groupTeams])
    const teamSearch = ref('')
    const autocompleteOpen = ref(false)

    const selectedTeamIds = computed(() => new Set(selectedTeams.value.map((team) => team.id)))

    const filteredTeams = computed(() => {
        const normalizedSearch = teamSearch.value.trim().toLowerCase()

        return teams.value.filter((team) => {
            if (selectedTeamIds.value.has(team.id)) {
                return false
            }

            if (!normalizedSearch) {
                return true
            }

            return team.name.toLowerCase().includes(normalizedSearch)
        })
    })

    const teamsById = computed(() => new Map(teams.value.map((team) => [team.id, team])))

    const addTeam = (teamId) => {
        const team = teamsById.value.get(teamId)

        if (!team) {
            return
        }

        selectedTeams.value.push(team)
        teamSearch.value = ''
    }

    const removeTeam = (teamId) => {
        selectedTeams.value = selectedTeams.value.filter((team) => team.id !== teamId)
    }

    return {
        selectedTeams,
        selectedTeamIds,
        filteredTeams,
        teamSearch,
        autocompleteOpen,
        addTeam,
        removeTeam,
    }
}

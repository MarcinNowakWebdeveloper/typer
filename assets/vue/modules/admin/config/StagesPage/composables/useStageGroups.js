import { computed, ref } from 'vue'
import api from '@/services/api.js'

export function useStageGroups(stageGroups, groupInput) {
    const groups = ref([])
    const groupsLoading = ref(true)
    const groupSearch = ref('')
    const autocompleteOpen = ref(false)
    const selectedGroups = ref(stageGroups.map((stageGroup) => stageGroup.group))
    const selectedGroupIds = computed(() => selectedGroups.value.map((group) => group.id))
    const filteredGroups = computed(() => {
        const normalizedSearch = groupSearch.value.trim().toLowerCase()

        return groups.value.filter((group) => {
            if (selectedGroupIds.value.includes(group.id)) {
                return false
            }

            if (!normalizedSearch) {
                return true
            }

            return group.name.toLowerCase().includes(normalizedSearch)
        })
    })
    const loadGroups = async () => {
        const response = await api.get('/api/admin/config/group/list')

        groups.value = response.data
        groupsLoading.value = false
    }
    const addGroup = (groupId) => {
        const group = groups.value.find((item) => item.id === Number(groupId))

        if (!group) {
            return
        }

        selectedGroups.value.push(group)
        groupSearch.value = ''
        autocompleteOpen.value = false
        groupInput.value?.blur()
    }
    const removeGroup = (groupId) => {
        selectedGroups.value = selectedGroups.value.filter((group) => group.id !== groupId)
    }

    return {
        groupsLoading,
        selectedGroups,
        selectedGroupIds,
        filteredGroups,
        groupSearch,
        autocompleteOpen,
        loadGroups,
        addGroup,
        removeGroup,
    }
}

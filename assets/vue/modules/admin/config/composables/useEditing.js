import { ref } from 'vue'

export function useEditing(load) {
    const editingIds = ref([])

    const edit = (id) => {
        if (!editingIds.value.includes(id)) {
            editingIds.value.push(id)
        }
    }

    const close = (id) => {
        editingIds.value = editingIds.value.filter((i) => i !== id)
    }

    const edited = async (id) => {
        await load()

        close(id)
    }

    return {
        editingIds,
        edit,
        close,
        edited,
    }
}

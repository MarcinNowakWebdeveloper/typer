import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

export function useCrudList(loadFunction, removeFunction) {
    const { t } = useI18n()
    const items = ref([])
    const loading = ref(true)

    const load = async () => {
        loading.value = true

        items.value = await loadFunction()

        loading.value = false
    }

    const remove = async (id) => {
        try {
            await removeFunction(id)

            await load()
        } catch (e) {
            alert(e.response?.data?.message ?? t('common.errors.500'))
        }
    }

    return {
        items,
        loading,
        load,
        remove,
    }
}

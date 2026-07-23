import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast.js'

export function useCrudList(loadFunction, removeFunction) {
    const { t } = useI18n()
    const items = ref([])
    const loading = ref(true)

    const load = async () => {
        loading.value = true

        try {
            items.value = await loadFunction()
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        }

        loading.value = false
    }

    const remove = async (id) => {
        try {
            await removeFunction(id)
            await load()
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        }
    }

    return {
        items,
        loading,
        load,
        remove,
    }
}

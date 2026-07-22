import { ref } from 'vue'
import api from '@/services/api.js'
import toast from '@/services/toast.js'
import { useI18n } from 'vue-i18n'

export function useUsers() {
    const { t } = useI18n()
    const users = ref([])
    const page = ref(1)
    const status = ref('all')
    const pagination = ref(null)

    const loadUsers = async () => {
        try {
            const { data } = await api.get('/api/admin/users', {
                params: {
                    page: page.value,
                    status: status.value,
                },
            })
            users.value = data.items
            pagination.value = data.pagination
        } catch (e) {
            toast.error(e.response?.data?.message ?? t('common.errors.500'))
        }
    }

    const changeStatus = async (newStatus) => {
        status.value = newStatus

        page.value = 1

        await loadUsers()
    }

    const nextPage = async () => {
        page.value++

        await loadUsers()
    }

    const previousPage = async () => {
        page.value--

        await loadUsers()
    }

    const userFilters = [
        {
            value: 'all',
            label: 'admin.users.filters.all',
        },
        {
            value: 'active',
            label: 'admin.users.filters.active',
        },
        {
            value: 'inactive',
            label: 'admin.users.filters.inactive',
        },
        {
            value: 'unconfirmed',
            label: 'admin.users.filters.unconfirmed',
        },
    ]

    return {
        users,
        page,
        status,
        pagination,
        loadUsers,
        changeStatus,
        nextPage,
        previousPage,
        userFilters,
    }
}

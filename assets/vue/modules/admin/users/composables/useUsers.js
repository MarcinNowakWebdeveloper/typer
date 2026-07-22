import { ref } from 'vue'
import api from '@/services/api.js'

export function useUsers() {
    const users = ref([])
    const page = ref(1)
    const status = ref('all')
    const pagination = ref(null)

    const loadUsers = async () => {
        const response = await api.get('/api/admin/users', {
            params: {
                page: page.value,
                status: status.value,
            },
        })
        users.value = response.data.items
        pagination.value = response.data.pagination
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

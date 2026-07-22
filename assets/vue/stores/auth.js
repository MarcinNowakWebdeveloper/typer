import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        initialized: false,
        user: null,
    }),

    getters: {
        isLogged: (state) => !!state.user,

        isAdmin: (state) => state.user?.roles?.includes('ROLE_ADMIN') ?? false,
    },

    actions: {
        async loadUser() {
            try {
                const response = await api.get('/api/me')

                this.user = response.data.user
            } catch {
                this.user = null
            } finally {
                this.initialized = true
            }
        },

        async login(credentials) {
            await api.post('/api/login', credentials)

            await this.loadUser()
        },

        async logout() {
            await api.post('/api/logout')

            this.user = null
        },
    },
})

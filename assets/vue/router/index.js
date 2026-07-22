import { createRouter, createWebHistory } from 'vue-router'

import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/layouts/AppLayout.vue'
import DashboardPage from '@/modules/dashboard/DashboardPage.vue'

import LoginPage from '@/modules/auth/LoginPage.vue'
import RegisterPage from '@/modules/auth/RegisterPage.vue'
import WaitingForActivationPage from '@/modules/auth/WaitingForActivationPage.vue'
import VerifyFailedPage from '@/modules/auth/VerifyFailedPage.vue'

import UsersPage from '@/modules/admin/users/UsersPage.vue'

const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            {
                path: '',
                name: 'dashboard',
                component: DashboardPage,
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: 'admin/users',
                name: 'users',
                component: UsersPage,
                meta: {
                    requiresAdmin: true,
                },
            },
            {
                path: '/login',
                name: 'login',
                component: LoginPage,
                meta: {
                    guestOnly: true,
                },
            },
            {
                path: '/register',
                name: 'register',
                component: RegisterPage,
                meta: {
                    guestOnly: true,
                },
            },
            {
                path: '/waiting-for-activation',
                component: WaitingForActivationPage,
            },
            {
                path: '/verify-failed',
                component: VerifyFailedPage,
            },
        ],
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach(async (to) => {
    const auth = useAuthStore()

    if (!auth.initialized) {
        await auth.loadUser()
    }

    if (to.meta.requiresAuth && !auth.isLogged) {
        return '/login'
    }

    if (to.meta.requiresAdmin && !auth.isAdmin) {
        return '/'
    }

    if (to.meta.guestOnly && auth.isLogged) {
        return '/'
    }
})

export default router

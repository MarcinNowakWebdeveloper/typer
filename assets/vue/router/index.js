import { createRouter, createWebHistory } from 'vue-router'

import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/layouts/AppLayout.vue'
import DashboardPage from '@/modules/dashboard/DashboardPage.vue'

import LoginPage from '@/modules/auth/LoginPage.vue'
import RegisterPage from '@/modules/auth/RegisterPage.vue'
import WaitingForActivationPage from '@/modules/auth/WaitingForActivationPage.vue'
import VerifyFailedPage from '@/modules/auth/VerifyFailedPage.vue'

import UsersPage from '@/modules/admin/users/UsersPage.vue'
import ConfigPage from '@/modules/admin/config/ConfigPage.vue'

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
                path: 'config',
                name: 'config',
                component: ConfigPage,
                meta: {
                    requiresAdmin: true,
                },
                children: [
                    {
                        path: '',
                        name: 'config_home',
                        redirect: { name: 'config_teams' },
                    },
                    {
                        path: 'teams',
                        name: 'config_teams',
                        component: () => import('@/modules/admin/config/TeamsPage.vue'),
                    },
                    {
                        path: 'groups',
                        name: 'config_groups',
                        component: () => import('@/modules/admin/config/GroupsPage.vue'),
                    },
                    {
                        path: 'stages',
                        name: 'config_stages',
                        component: () => import('@/modules/admin/config/StagesPage.vue'),
                    },
                    {
                        path: 'stages/:id/games',
                        name: 'config_stages_games',
                        component: () => import('@/modules/admin/config/StagesPage/GamesPage.vue'),
                    },
                ],
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

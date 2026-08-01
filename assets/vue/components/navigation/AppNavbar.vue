<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import UserAvatar from '@/components/users/UserAvatar.vue'
import { useI18n } from 'vue-i18n'
import JokerIcon from '@/../icons/card-joker-svgrepo-com.svg'

const auth = useAuthStore()
const router = useRouter()
const { t } = useI18n()

const logout = async () => {
    await auth.logout()
    router.push({ name: 'login' })
}

const login = async () => {
    router.push({ name: 'login' })
}

const registry = async () => {
    router.push({ name: 'register' })
}
</script>

<template>
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <div class="logo d-flex align-items-center">
                <i class="bi bi-trophy fw-bold me-2 color-primary" />
                <router-link class="navbar-brand fw-bold text-md-start" :to="{ name: 'dashboard' }" active-class="">
                    {{ t('common.title') }}
                </router-link>
            </div>

            <div v-if="auth.isLogged" class="navbar-nav ms-4">
                <router-link class="nav-link d-flex align-items-center px-2" :to="{ name: 'stage' }">
                    <i class="bi bi-receipt me-2" />{{ t('components.navigation.stage') }}
                </router-link>

                <router-link class="nav-link d-flex align-items-center px-2" :to="{ name: 'joker' }">
                    <JokerIcon class="joker-icon me-2" />{{ t('components.navigation.joker') }}
                </router-link>

                <router-link
                    v-if="auth.isAdmin"
                    class="nav-link d-flex align-items-center px-2"
                    :to="{ name: 'users' }"
                >
                    <i class="bi bi-person-fill me-2" />{{ t('components.navigation.users') }}
                </router-link>

                <router-link
                    v-if="auth.isAdmin"
                    class="nav-link d-flex align-items-center px-2"
                    :to="{ name: 'config_home' }"
                >
                    <i class="bi bi-gear me-2" />{{ t('components.navigation.configuration') }}
                </router-link>
            </div>

            <div v-if="auth.isLogged" class="ms-auto d-flex align-items-center gap-3">
                <div>
                    <UserAvatar :name="auth.user?.name" :color="auth.user?.color" />
                    <div class="points text-secondary f08">
                        {{ auth.user?.points }} {{ t('components.navigation.pts') }}.
                    </div>
                </div>

                <button class="btn btn-outline-secondary" @click="logout">
                    <i class="bi bi-box-arrow-right pe-1" />{{ t('components.navigation.logout') }}
                </button>
            </div>
            <div v-else class="ms-auto d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary" @click="login">
                    <i class="bi bi-box-arrow-left pe-1" />{{ t('components.navigation.login') }}
                </button>
                <button class="btn btn-outline-secondary" @click="registry">
                    <i class="bi bi-person-add pe-1" />{{ t('components.navigation.registry') }}
                </button>
            </div>
        </div>
    </nav>
</template>

<style lang="scss">
nav.navbar {
    a {
        text-decoration: none;
    }

    .logo {
        i {
            font-size: 2rem;
        }
    }

    .nav-link:hover {
        background: rgba(91, 76, 255, 0.12);
        color: var(--primary);
        border-radius: 10px;
    }

    .joker-icon {
        height: 30px;
    }

    .router-link-active {
        background: rgba(91, 76, 255, 0.12);
        color: var(--primary);
        border-radius: 10px;
        font-weight: 600;
    }
}
</style>

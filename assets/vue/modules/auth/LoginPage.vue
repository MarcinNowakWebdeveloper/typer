<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast'

const router = useRouter()
const auth = useAuthStore()
const { t } = useI18n()

const form = reactive({
    email: '',
    password: '',
})

const login = async () => {
    try {
        await auth.login(form)

        router.push({ name: 'dashboard' })
    } catch (e) {
        toast.error(e.response.data.message)
    }
}
</script>

<template>
    <div class="card">
        <h1 class="mb-4"><i class="bi bi-box-arrow-left color-primary pe-2" />{{ t('auth.loginPage.title') }}</h1>

        <form @submit.prevent="login">
            <div class="mb-3 row">
                <label for="email" class="col-sm-1 col-form-label">{{ t('auth.loginPage.form.email') }}</label>
                <input
                    id="email"
                    v-model="form.email"
                    required
                    type="email"
                    class="col-sm-3"
                    placeholder="name@example.com"
                />
            </div>
            <div class="mb-3 row">
                <label for="password" class="col-sm-1 col-form-label">{{ t('auth.loginPage.form.password') }}</label>
                <input id="password" v-model="form.password" required type="password" class="col-sm-3" />
            </div>
            <button class="btn btn-primary col-sm-1" type="submit">{{ t('auth.loginPage.form.submit') }}</button>
        </form>
    </div>
</template>

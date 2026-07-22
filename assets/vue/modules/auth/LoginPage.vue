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
        const data = await auth.login(form)

        if (data.success === true) {
            await auth.loadUser()
            router.push({ name: 'dashboard' })
        } else {
            let message = data.message ? data.message : t('common.errors.500')
            toast.error(message)
        }
    } catch (e) {
        toast.error(e.response?.data?.message ?? t('common.errors.500'))
    }
}
</script>

<template>
    <div class="card">
        <h1 class="mb-4"><i class="bi bi-box-arrow-left color-primary pe-2" />{{ t('auth.loginPage.title') }}</h1>

        <form @submit.prevent="login">
            <div class="mb-3 form-group row">
                <label for="email" class="col-3 col-sm-2 col-xl-1 col-form-label">{{
                    t('auth.loginPage.form.email')
                }}</label>
                <div class="col-9 col-sm-6 col-md-5 col-xl-3">
                    <input
                        id="email"
                        v-model="form.email"
                        required
                        type="email"
                        class="form-control"
                        placeholder="name@example.com"
                    />
                </div>
            </div>
            <div class="mb-3 form-group row">
                <label for="password" class="col-3 col-sm-2 col-xl-1 col-form-label">{{
                    t('auth.loginPage.form.password')
                }}</label>
                <div class="col-9 col-sm-6 col-md-5 col-xl-3">
                    <input id="password" v-model="form.password" required type="password" class="form-control" />
                </div>
            </div>
            <button class="btn btn-primary" type="submit">{{ t('auth.loginPage.form.submit') }}</button>
        </form>
    </div>
</template>

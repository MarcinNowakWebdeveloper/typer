<script setup>
import { reactive } from 'vue'
import api from '@/services/api.js'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast'
const { t } = useI18n()

const form = reactive({
    email: '',
    password: '',
    name: '',
})

const register = async () => {
    try {
        const response = await api.post('/api/register', form)
        if (response.data.success === true) {
            toast.error(t('auth.registerPage.response.success'))
        } else {
            toast.error(response.data.message)
        }
    } catch (e) {
        toast.error(t('common.errors.500'))
        console.log(e)
    }
}
</script>
<template>
    <div class="card">
        <h1 class="mb-4"><i class="bi bi-person-add color-primary pe-2" />{{ t('auth.registerPage.title') }}</h1>

        <form @submit.prevent="register">
            <div class="mb-3 row">
                <label for="name" class="col-sm-2 col-form-label">{{ t('auth.registerPage.form.name') }}</label>
                <input id="name" v-model="form.name" required type="text" class="col-sm-3" />
            </div>
            <div class="mb-3 row">
                <label for="email" class="col-sm-2 col-form-label">{{ t('auth.registerPage.form.email') }}</label>
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
                <label for="password" class="col-sm-2 col-form-label">{{ t('auth.registerPage.form.password') }}</label>
                <input id="password" v-model="form.password" required type="password" class="col-sm-3" />
            </div>
            <button class="btn btn-primary col-sm-1" type="submit">{{ t('auth.registerPage.form.submit') }}</button>
        </form>
    </div>
</template>

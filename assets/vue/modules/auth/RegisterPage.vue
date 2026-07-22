<script setup>
import { useI18n } from 'vue-i18n'
import { useRegistryForm } from '@/modules/auth/composables/useRegistryForm.js'

const { t } = useI18n()

const { loading, form, register } = useRegistryForm()
</script>
<template>
    <div class="card">
        <h1 class="mb-4"><i class="bi bi-person-add color-primary pe-2" />{{ t('auth.registerPage.title') }}</h1>

        <form @submit.prevent="register">
            <div class="mb-3 form-group row">
                <label for="name" class="col-12 col-sm-3 col-xl-2 col-form-label">{{
                    t('auth.registerPage.form.name')
                }}</label>
                <div class="col-12 col-sm-6 col-md-5 col-xl-3">
                    <input id="name" v-model="form.name" required type="text" class="col-sm-3 form-control" />
                </div>
            </div>
            <div class="mb-3 form-group row">
                <label for="email" class="col-12 col-sm-3 col-xl-2 col-form-label">{{
                    t('auth.registerPage.form.email')
                }}</label>
                <div class="col-12 col-sm-6 col-md-5 col-xl-3">
                    <input
                        id="email"
                        v-model="form.email"
                        required
                        type="email"
                        class="col-sm-3 form-control"
                        placeholder="name@example.com"
                    />
                </div>
            </div>
            <div class="mb-3 form-group row">
                <label for="password" class="col-12 col-sm-3 col-xl-2 col-form-label">{{
                    t('auth.registerPage.form.password')
                }}</label>
                <div class="col-12 col-sm-6 col-md-5 col-xl-3">
                    <input
                        id="password"
                        v-model="form.password"
                        required
                        type="password"
                        class="col-sm-3 form-control"
                    />
                </div>
            </div>
            <button class="btn btn-primary" :disabled="loading" type="submit">
                {{ t('auth.registerPage.form.submit') }}
            </button>
        </form>
    </div>
</template>

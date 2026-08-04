<script setup>
import { onMounted, ref } from 'vue'
import api from '@/services/api.js'
import { useSettingsStore } from '@/stores/settings.js'
import AppLoader from '@/components/loaders/AppLoader.vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth.js'

const { t } = useI18n()
const strategies = ref({})
const loading = ref(true)
const settings = useSettingsStore()
const auth = useAuthStore()

const loadPointCountingStrategies = async () => {
    try {
        loading.value = true
        const { data } = await api.get('/api/point-counting-strategies')
        strategies.value = data

        settings.pointCountingStrategies = settings.pointCountingStrategies
            ? settings.pointCountingStrategies
            : strategies.value.find((s) => s.isDefault === true)?.id
    } catch (e) {
        alert(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
    }
}

const setStrategy = async (id) => {
    settings.pointCountingStrategies = id
    await auth.loadUser()
}

onMounted(loadPointCountingStrategies)
</script>
<template>
    <AppLoader v-if="loading" />
    <div v-else class="profile">
        <div class="card p-3">
            <h1><i class="bi bi-person-gear color-primary me-2"></i>{{ t('user.profile.title') }}</h1>
        </div>

        <div class="card p-3 mt-3">
            <h2>{{ t('user.profile.pointsStrategy.title') }}</h2>

            <div v-for="strategy in strategies" :key="strategy.id" class="card p-3 mb-2">
                <div class="row align-items-start">
                    <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                        <input
                            :id="strategy.code"
                            v-model="settings.pointCountingStrategies"
                            type="radio"
                            :value="strategy.id"
                            class="me-2"
                            @click="setStrategy(strategy.id)"
                        />
                        <label :for="strategy.code">{{ strategy.name }}</label>
                        <span v-if="strategy.isDefault" class="is-default fw-light fs-6 ms-1">
                            ({{ t('user.profile.pointsStrategy.default') }})
                        </span>
                    </div>
                    <div class="col-12 col-lg-10" v-html="strategy.description"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.profile {
    .is-default {
        font-size: 0.8em !important;
    }
}
</style>

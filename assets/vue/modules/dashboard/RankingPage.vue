<script setup>
import api from '@/services/api.js'
import { onMounted, ref } from 'vue'
import RankingPodium from '@/modules/dashboard/RankingPage/RankingPodium.vue'
import { useI18n } from 'vue-i18n'
import AppLoader from '@/components/loaders/AppLoader.vue'
import RankingTable from '@/modules/dashboard/RankingPage/RankingTable.vue'
import AppNoData from '@/components/no_data/AppNoData.vue'
import { useSettingsStore } from '@/stores/settings.js'

const { t } = useI18n()
const ranking = ref(null)
const loading = ref(true)

const loadRanking = async () => {
    try {
        loading.value = true

        let url = '/api/ranking'
        let settings = useSettingsStore()
        if (settings.pointCountingStrategies) {
            url += '?pointCountingStrategies=' + settings.pointCountingStrategies
        }
        const { data } = await api.get(url)
        ranking.value = data
    } catch (e) {
        alert(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
    }
}

onMounted(loadRanking)
</script>

<template>
    <AppLoader v-if="loading" />
    <div v-else-if="ranking.length > 0" class="ranking">
        <div class="card mb-4">
            <RankingPodium v-if="ranking" :ranking="ranking" />
        </div>

        <div class="card p-3">
            <h4>{{ t('dashboard.ranking.title') }}</h4>
            <RankingTable v-if="ranking" :ranking="ranking" />
        </div>
    </div>
    <AppNoData v-else />
</template>

<script setup>
import { useRankingPodium } from '@/modules/dashboard/RankingPage/composables/useRankingPodium.js'
import { useI18n } from 'vue-i18n'
import PodiumPosition from '@/modules/dashboard/RankingPage/RankingPodium/PodiumPosition.vue'

const { ranking } = defineProps({
    ranking: {
        type: Array,
        required: true,
    },
})

const { t } = useI18n()
const { first, second, third } = useRankingPodium(ranking)
</script>
<template>
    <div v-if="first" class="podium p-3">
        <h2><i class="bi bi-trophy fw-bold me-2 text-warning"></i>{{ t('dashboard.ranking.podium.title') }}</h2>
        <div class="podium-content">
            <PodiumPosition :first-points="first.points" :order="2" :item="second" />
            <PodiumPosition :first-points="first.points" :order="1" :item="first" />
            <PodiumPosition :first-points="first.points" :order="3" :item="third" />
        </div>
    </div>
</template>

<style lang="scss">
.podium {
    .podium-content {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        gap: 40px;
        padding: 10px 30px 30px;
    }
}
</style>

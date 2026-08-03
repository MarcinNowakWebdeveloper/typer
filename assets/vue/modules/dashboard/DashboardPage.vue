<script setup>
import RankingPage from '@/modules/dashboard/RankingPage.vue'
import PointsLineChart from '@/modules/charts/PointsLineChart.vue'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

const visible = ref('rankingPage')

const { t } = useI18n()
</script>
<template>
    <div class="dashboard">
        <div class="card">
            <div class="d-flex p-3">
                <span
                    :class="[
                        'py-2 px-4 me-2 text-secondary rounded nav-link d-flex align-items-center',
                        visible === 'rankingPage' ? 'active fw-bolder' : '',
                    ]"
                    @click="visible = 'rankingPage'"
                >
                    {{ t('dashboard.navigation.ranking') }}
                </span>
                <span
                    :class="[
                        'py-2 px-4 me-2 text-secondary rounded nav-link text-center',
                        visible === 'pointsLineChart' ? 'active fw-bolder' : '',
                    ]"
                    @click="visible = 'pointsLineChart'"
                >
                    {{ t('dashboard.navigation.pointsLineChart') }}
                </span>
            </div>
        </div>
        <div class="mt-3 px-0">
            <RankingPage v-if="visible === 'rankingPage'" />
            <PointsLineChart v-if="visible === 'pointsLineChart'" />
        </div>
    </div>
</template>
<style lang="scss">
.dashboard {
    .nav-link {
        cursor: pointer;
        &.active {
            background: rgb(var(--primary-rgb), 0.12) !important;
            color: var(--primary) !important;
        }
        &:hover {
            background: rgb(var(--primary-rgb), 0.12) !important;
            color: var(--primary) !important;
        }
    }
}
</style>

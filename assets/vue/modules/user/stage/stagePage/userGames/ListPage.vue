<script setup>
import { onMounted, ref } from 'vue'
import api from '@/services/api.js'
import AdminPage from '@/modules/user/stage/stagePage/userGames/list/AdminPage.vue'
import ExpiredPage from '@/modules/user/stage/stagePage/userGames/list/ExpiredPage.vue'
import { useI18n } from 'vue-i18n'
import AppLoader from '@/components/loaders/AppLoader.vue'
import { useSettingsStore } from '@/stores/settings'

const props = defineProps({
    gameId: {
        type: Number,
        required: true,
    },
})

const { t } = useI18n()
const predictions = ref({})
const predictionsOpened = ref(false)
const pointsSet = ref(null)
const expired = ref(null)
const maxPoints = ref(null)
const loading = ref(true)

const loadStage = async () => {
    let url = '/api/game:' + props.gameId + '/predictions'
    let settings = useSettingsStore()
    if (settings.pointCountingStrategies) {
        url += '?pointCountingStrategies=' + settings.pointCountingStrategies
    }

    try {
        loading.value = true
        const response = await api.get(url)
        if (Array.isArray(response.data) && response.data.length === 0) {
            return
        }

        expired.value = response.data.expired
        pointsSet.value = response.data.points_set
        predictions.value = response.data.data
        maxPoints.value = response.data.max_points
    } catch (e) {
        alert(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
    }
}

onMounted(loadStage)
</script>
<template>
    <AppLoader v-if="loading" />
    <div v-if="!loading && !expired !== null" class="predictions">
        <div
            v-if="predictions"
            :class="[
                'button py-2 px-4 d-flex justify-content-between align-items-center pointer',
                !predictionsOpened ? 'bg-light' : 'bg-primary-subtle open',
            ]"
            @click="predictionsOpened = !predictionsOpened"
        >
            <span
                ><i class="bi bi-people me-2 fw-bold"></i
                ><span class="fw-bold">{{ t('user.stage.game.bets') }}:</span></span
            >

            <i :class="['bi', predictionsOpened ? 'bi-chevron-compact-up' : 'bi-chevron-compact-down']"></i>
        </div>
        <Transition name="collapse" class="content">
            <AdminPage v-if="predictionsOpened && !expired" :predictions="predictions" />
            <ExpiredPage
                v-else-if="predictionsOpened && expired"
                :predictions="predictions"
                :points-set="pointsSet"
                :max-points="maxPoints"
            ></ExpiredPage>
        </Transition>
    </div>
</template>
<style lang="scss">
.game-view {
    .predictions {
        .button {
            border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius) !important;
            border-bottom: 1px solid var(--bs-primary-bg-subtle) !important;

            &.open {
                border-radius: 0 !important;
                color: var(--bs-primary) !important;
            }

            &:hover {
                background-color: var(--bs-primary-bg-subtle) !important;
                color: var(--bs-primary) !important;
            }

            i {
                font-size: 18px;
            }

            span {
                font-size: 13px;
            }
        }
    }
}
</style>

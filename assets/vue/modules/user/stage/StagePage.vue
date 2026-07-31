<script setup>
import { useRoute } from 'vue-router'
import { ref, watch } from 'vue'
import AppLoader from '@/components/loaders/AppLoader.vue'
import Reminder from '@/modules/user/stage/stagePage/StageReminder.vue'
import JokerReminder from '@/modules/user/stage/stagePage/JokerReminder.vue'
import UserGames from '@/modules/user/stage/stagePage/UserGames.vue'
import Navigations from '@/modules/user/stage/stagePage/StageNavigations.vue'
import { useStage } from '@/modules/user/stage/stagePage/composables/useStage'
import { useStageGames } from '@/modules/user/stage/stagePage/composables/useStageGames'
import { useStagePredictions } from '@/modules/user/stage/stagePage/composables/useStagePredictions'

import { useI18n } from 'vue-i18n'
import AppNoData from '@/components/no_data/AppNoData.vue'
import toast from '@/services/toast.js'

const { t } = useI18n()
const route = useRoute()
const loading = ref(true)

const { stage, stages, showFutureGames, loadStage } = useStage()

const { futureGames, pastGames, gamesIds, showTypeChooser } = useStageGames(stage)

const { predictions, loadPredictions } = useStagePredictions()

const loadData = async () => {
    loading.value = true

    try {
        await loadStage()
        await loadPredictions(gamesIds.value)
    } catch (e) {
        toast.error(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
    }
}

watch(() => route.params.id, loadData, {
    immediate: true,
})
</script>

<template>
    <AppLoader v-if="loading" />
    <div v-else-if="stage.start_date" class="stage">
        <Reminder :stage="stage" />

        <JokerReminder />

        <div class="games card container mt-3 px-0">
            <Navigations :stages="stages" :stage-id="stage.id" />
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>{{ t('user.stage.games.title') }}</h2>
                    <div v-if="showTypeChooser" class="chose-type text-end">
                        <button
                            :class="['btn future-games', showFutureGames ? 'btn-primary' : 'btn-light']"
                            @click="showFutureGames = true"
                        >
                            <i class="bi bi-calendar-date m-2"></i>{{ t('user.stage.games.future') }}
                            <span class="p-1 length f08">{{ futureGames.length }}</span>
                        </button>
                        <button
                            :class="['btn past-games', showFutureGames ? 'btn-light' : 'btn-primary']"
                            @click="showFutureGames = false"
                        >
                            <i class="bi bi-check-circle m-2"></i>{{ t('user.stage.games.past') }}
                            <span class="p-1 length f08">{{ pastGames.length }}</span>
                        </button>
                    </div>
                </div>
                <div v-if="showFutureGames" class="future-games">
                    <div v-for="game in futureGames" :key="game.id">
                        <UserGames
                            :game="game"
                            :user-away-goals="predictions[game.id]?.awayGoals"
                            :user-home-goals="predictions[game.id]?.homeGoals"
                        />
                    </div>
                </div>
                <div v-else class="past-games">
                    <div v-for="game in pastGames" :key="game.id">
                        <UserGames
                            :game="game"
                            :user-away-goals="predictions[game.id]?.awayGoals"
                            :user-home-goals="predictions[game.id]?.homeGoals"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <AppNoData v-else />
</template>
<style lang="scss">
.stage {
    .games {
        .stage-tab {
            border-radius: 0.375rem 0.375rem 0 0;
            border: 1px solid rgba(223, 224, 225, 0.25);
            border-bottom-style: none;
            background-color: rgba(223, 224, 225, 0.25);

            &:hover {
                border-color: rgba(91, 76, 255, 0.12);
                background-color: rgba(91, 76, 255, 0.12);
                color: rgb(91, 76, 255) !important;
            }

            &.active {
                border-color: rgba(91, 76, 255, 0.12);
                background-color: rgba(91, 76, 255, 0.12);
                color: rgb(91, 76, 255) !important;
            }
        }

        .chose-type {
            .length {
                border-radius: 50%;
                width: 25px;
                height: 25px;

                display: inline-flex;
                align-items: center;
                justify-content: center;

                min-width: 24px;
            }

            .btn-primary {
                .length {
                    background-color: rgba(100, 155, 238, 0.75);
                }

                &:hover {
                    .length {
                        background-color: rgba(100, 155, 238, 0.95);
                    }
                }
            }

            .btn-light {
                .length {
                    border: 1px solid rgba(223, 224, 225, 0.95);
                }

                &:hover {
                    .length {
                        background-color: rgba(223, 224, 225, 0.95);
                    }
                }
            }

            .future-games {
                border-radius: 0.375rem 0 0 0.375rem;
            }
            .past-games {
                border-radius: 0 0.375rem 0.375rem 0;
                @media (max-width: 930px) {
                    border-radius: 0.375rem 0 0 0.375rem;
                }
            }
        }

        .row {
            margin-right: initial !important;
            margin-left: initial !important;
        }
    }
}
</style>

<script setup>
import { onMounted } from 'vue'
import GameForm from '@/modules/admin/config/StagesPage/GamesPage/GameForm.vue'
import { useI18n } from 'vue-i18n'
import { useGamesList } from '@/modules/admin/config/StagesPage/composables/useGamesList.js'
import { useGamesForm } from '@/modules/admin/config/StagesPage/composables/useGamesForm.js'
import { useRoute } from 'vue-router'
import AppLoader from '@/components/loaders/AppLoader.vue'

const { t } = useI18n()
const route = useRoute()
const { stageGroup, loading, loadGames } = useGamesList(route.params.id)
const { editingIds, edit, edited, remove, close } = useGamesForm(loadGames)

onMounted(loadGames)
</script>
<template>
    <AppLoader v-if="loading" />
    <div v-else class="stages-list">
        <GameForm :stage-group="stageGroup" @saved="loadGames()"></GameForm>

        <div class="row align-items-start">
            <div v-for="game in stageGroup.games" :key="game.id" class="col-12 col-lg-6 col">
                <div
                    v-if="!editingIds.has(game.id)"
                    class="p-3 border border-1 mt-2 rounded d-flex justify-content-between align-items-center"
                >
                    <div class="col-9">
                        <div class="d-grid align-items-center game-description text-end">
                            <div class="fw-bold d-flex align-items-center gap-3 justify-content-end">
                                <div class="d-none d-lg-inline">{{ game.homeTeam.name }}</div>
                                <img
                                    v-if="game.homeTeam.logo"
                                    :src="`/api/file/${game.homeTeam.logo?.id}`"
                                    :alt="game.homeTeam.logo?.originName"
                                    class="team-logo"
                                    loading="lazy"
                                />
                                <span v-if="game.homeGoals">{{ game.homeGoals }}</span>
                            </div>
                            <div class="px-2 fw-bold text-secondary">VS</div>
                            <div class="text-start fw-bold d-flex align-items-center gap-3">
                                <span v-if="game.awayGoals">{{ game.awayGoals }}</span>
                                <img
                                    v-if="game.homeTeam.logo"
                                    :src="`/api/file/${game.awayTeam.logo?.id}`"
                                    :alt="game.awayTeam.logo?.originName"
                                    class="team-logo"
                                    loading="lazy"
                                />
                                <div class="d-none d-lg-inline">{{ game.awayTeam.name }}</div>
                            </div>
                        </div>
                        <div class="fs-6 text-center">
                            {{ game.date.split('-').slice(1).reverse().join('-') }} {{ game.time }}
                        </div>
                    </div>
                    <div class="col-3 text-end">
                        <button
                            class="btn me-2 text-primary bg-primary-subtle mb-2"
                            :title="t('common.action.edit')"
                            @click="edit(game.id)"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button
                            class="btn me-2 text-danger bg-danger-subtle mb-2"
                            :title="t('common.action.remove')"
                            @click="remove(game.id)"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>

                <div v-if="editingIds.has(game.id)" class="p-3 border border-1 mt-2 rounded">
                    <GameForm :stage-group="stageGroup" :game="game" @saved="edited" @close="close"></GameForm>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.game-description {
    grid-template-columns: 1fr auto 1fr;

    .team-logo {
        height: 30px;
    }
}
</style>

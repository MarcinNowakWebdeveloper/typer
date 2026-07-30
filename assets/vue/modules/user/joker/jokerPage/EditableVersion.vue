<script setup>
import { onMounted, ref } from 'vue'
import api from '@/services/api.js'
import JokerIcon from '../../../../../icons/card-joker-svgrepo-com.svg'
import AppLoader from '@/components/loaders/AppLoader.vue'
import { useI18n } from 'vue-i18n'
import { useTeams } from '@/composables/useTeams.js'
import AppNoData from '@/components/no_data/AppNoData.vue'

const props = defineProps({
    jokerId: {
        type: Number,
        required: false,
        default: null,
    },
})

const { t } = useI18n()
const loading = ref(false)
const jokerId = ref(props.jokerId)

const { loadTeams, teams, teamsLoading } = useTeams()

const setJoker = async (teamId) => {
    try {
        loading.value = true
        await api.put('/api/user/joker/set', { teamId })
        jokerId.value = teamId
    } catch (e) {
        alert(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
    }
}

onMounted(loadTeams)
</script>
<template>
    <AppLoader v-if="teamsLoading" />
    <div v-else-if="teams.length > 0" class="card">
        <div class="joker row">
            <div>
                <h2><JokerIcon class="joker-icon color-primary me-2" />{{ t('user.joker.editablePage.title') }}</h2>
                {{ t('user.joker.editablePage.description') }}
            </div>
            <AppLoader v-if="loading" />
            <div v-else class="teams-list text-center">
                <div
                    v-for="team in teams"
                    :key="team.id"
                    ref="joker-cards"
                    class="col-auto border border-1 mt-3 me-2 p-3 rounded joker-team-card"
                    @click="setJoker(team.id)"
                >
                    <div class="text-center">
                        <img
                            v-if="team.logo"
                            :src="`/api/file/${team.logo.id}`"
                            :alt="team.logo.originName"
                            class="team-logo"
                            loading="lazy"
                        />
                    </div>
                    <div class="text-center mt-1">{{ team.name }}</div>
                    <div class="text-center mt-1">
                        <JokerIcon v-if="jokerId === team.id" class="joker-icon color-primary" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <AppNoData v-else />
</template>
<style lang="scss">
.joker {
    h1 > .joker-icon {
        height: 46px;
    }

    .teams-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;

        .joker-team-card {
            cursor: pointer;
            width: 163px;

            img {
                width: 55px;

                &.team-logo {
                    border: 1px solid #eeedf1;
                }
            }
        }

        .joker-icon {
            height: 46px;
        }
    }
}
</style>

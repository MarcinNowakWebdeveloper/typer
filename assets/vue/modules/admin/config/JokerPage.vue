<script setup>
import api from '@/services/api.js'
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import JokerIcon from '../../../../icons/card-joker-svgrepo-com.svg'
import AppLoader from '@/components/loaders/AppLoader.vue'
import { useTeams } from '@/composables/useTeams.js'
import toast from '@/services/toast.js'

const { t } = useI18n()

const { loadTeams, teams, teamsLoading: loading } = useTeams()

const setJoker = async (teamId) => {
    try {
        loading.value = true
        await api.put('/api/admin/config/joker/set', { teamId: teamId })
    } catch (e) {
        toast.error(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
        await loadTeams()
    }
}

onMounted(loadTeams)
</script>
<template>
    <AppLoader v-if="loading" />
    <div v-else class="joker-edit">
        <div v-for="team in teams" :key="team.id" class="team p-3 text-center" @click="setJoker(team.id)">
            <img v-if="team.logo" :src="`/api/file/${team.logo.id}`" :alt="team.name" :title="team.name" class="logo" />
            <span class="text-truncate d-block mt-1">{{ team.name }}</span>
            <JokerIcon v-if="team.isJoker" class="joker-icon color-primary" />
        </div>
    </div>
</template>
<style lang="scss">
.joker-edit {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;

    .joker-icon {
        height: 30px;
    }

    .team {
        border: 1px solid rgba(var(--primary-rgb), 0.35);
        border-radius: 0.375rem;
        width: 100px;
        height: 110px;
        cursor: pointer;

        .logo {
            height: 30px;
        }
    }
}
</style>

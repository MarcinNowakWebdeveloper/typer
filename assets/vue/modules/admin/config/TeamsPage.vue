<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import TeamForm from '@/modules/admin/config/TeamsPage/TeamForm.vue'
import api from '@/services/api.js'
import { useCrudList } from '@/modules/admin/config/composables/useCrudList'
import { useEditing } from '@/modules/admin/config/composables/useEditing'
import AppLoader from '@/components/loaders/AppLoader.vue'

const { t } = useI18n()

const {
    items: teams,
    loading,
    load: loadTeams,
    remove,
} = useCrudList(
    async () => {
        const response = await api.get('/api/team/list')
        return response.data
    },

    async (id) => {
        await api.delete(`/api/admin/config/team:${id}/remove`)
    },
)

const { editingIds, edit, close, edited } = useEditing(loadTeams)

onMounted(loadTeams)
</script>

<template>
    <AppLoader v-if="loading" />
    <div v-else class="teams-list">
        <TeamForm @saved="loadTeams"></TeamForm>

        <div class="row align-items-start">
            <div v-for="team in teams" :key="team.id" class="col-12 col-lg-6 col">
                <div
                    v-if="!editingIds.includes(team.id)"
                    class="p-3 border border-1 mt-2 rounded d-flex justify-content-between align-items-center"
                >
                    <div>
                        <img v-if="team.logo" :src="`/api/file/${team.logo.id}`" class="team-logo" />
                        <span class="ms-2">{{ team.name }}</span>
                    </div>
                    <div>
                        <button
                            class="btn me-2 text-primary bg-primary-subtle mb-2"
                            :title="t('common.action.edit')"
                            @click="edit(team.id)"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button
                            class="btn me-2 text-danger bg-danger-subtle mb-2"
                            :title="t('common.action.remove')"
                            @click="remove(team.id)"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
                <div v-if="editingIds.includes(team.id)" class="p-3 border border-1 mt-2 rounded">
                    <TeamForm
                        :id="team.id"
                        :name="team.name"
                        :logo="team.logo?.id"
                        @saved="edited"
                        @close="close"
                    ></TeamForm>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.teams-list {
    .team-logo {
        width: 40px;
    }
}
</style>

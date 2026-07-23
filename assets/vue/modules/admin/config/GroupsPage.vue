<script setup>
import { onMounted } from 'vue'
import api from '@/services/api'
import { useI18n } from 'vue-i18n'
import GroupForm from '@/modules/admin/config/GroupsPage/GroupForm.vue'
import { useCrudList } from '@/modules/admin/config/composables/useCrudList'
import { useEditing } from '@/modules/admin/config/composables/useEditing'
import AppLoader from '@/components/loaders/AppLoader.vue'

const { t } = useI18n()

const {
    items: groups,
    loading,
    load: loadGroups,
    remove,
} = useCrudList(
    async () => {
        const response = await api.get('/api/admin/config/group/list')
        return response.data
    },

    async (groupId) => {
        await api.delete(`/api/admin/config/group:${groupId}/remove`)
    },
)

const { editingIds, edit, close, edited } = useEditing(loadGroups)

onMounted(loadGroups)
</script>

<template>
    <AppLoader v-if="loading" />
    <div v-else class="groups-list">
        <GroupForm @saved="loadGroups"></GroupForm>

        <div class="row align-items-start">
            <div v-for="group in groups" :key="group.id" class="col-12 col-lg-6 col">
                <div
                    v-if="!editingIds.includes(group.id)"
                    class="p-3 border border-1 mt-2 rounded d-flex justify-content-between align-items-center"
                >
                    <div>
                        <span class="fw-bold">{{ group.name }}</span>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <template v-for="team in group.teams" :key="team.id">
                                <img
                                    v-if="team.logo"
                                    :src="`/api/file/${team.logo.id}`"
                                    :alt="team.name"
                                    class="group-team-logo"
                                    loading="lazy"
                                />
                            </template>
                        </div>
                    </div>
                    <div>
                        <button
                            class="btn me-2 text-primary bg-primary-subtle mb-2"
                            :title="t('common.action.edit')"
                            @click="edit(group.id)"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button
                            class="btn me-2 text-danger bg-danger-subtle mb-2"
                            :title="t('common.action.remove')"
                            @click="remove(group.id)"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
                <div v-if="editingIds.includes(group.id)" class="p-3 border border-1 mt-2 rounded">
                    <GroupForm
                        :id="group.id"
                        :name="group.name"
                        :group-teams="group.teams"
                        @saved="edited"
                        @close="close"
                    ></GroupForm>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.groups-list {
    .group-team-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }
}
</style>

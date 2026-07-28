<script setup>
import { onMounted } from 'vue'
import api from '@/services/api'
import StageForm from '@/modules/admin/config/StagesPage/StageForm.vue'
import { useI18n } from 'vue-i18n'
import { useCrudList } from '@/modules/admin/config/composables/useCrudList'
import { useEditing } from '@/modules/admin/config/composables/useEditing'
import AppLoader from '@/components/loaders/AppLoader.vue'

const { t } = useI18n()

const {
    items: stages,
    loading,
    load: loadStages,
    remove,
} = useCrudList(
    async () => {
        const response = await api.get('/api/admin/config/stage/list')
        return response.data
    },

    async (stageId) => {
        await api.delete(`/api/admin/config/stage:${stageId}/remove`)
    },
)

const { editingIds, edit, close, edited } = useEditing(loadStages)

onMounted(loadStages)
</script>

<template>
    <AppLoader v-if="loading" />
    <div v-else class="stages-list">
        <StageForm @saved="loadStages()"></StageForm>

        <div class="row align-items-start">
            <div v-for="stage in stages" :key="stage.id" class="col-12 col-lg-6 col">
                <div
                    v-if="!editingIds.includes(stage.id)"
                    class="p-3 border border-1 mt-2 rounded d-flex justify-content-between align-items-center"
                >
                    <div>
                        <span class="fw-bold">{{ stage.name }}</span>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <template v-for="stageGroup in stage.groups" :key="stageGroup.id">
                                <div class="btn btn-light col-auto me-3">
                                    <div>{{ stageGroup.group.name }}</div>
                                    <div class="group-teams-count">
                                        {{ stageGroup.group.teams.length }}
                                        {{ t('admin.config.stagePage.stageGroup.teams') }}
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <button
                            class="btn me-2 text-primary bg-primary-subtle mb-2"
                            :title="t('common.action.edit')"
                            @click="edit(stage.id)"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button
                            class="btn me-2 text-danger bg-danger-subtle mb-2"
                            :title="t('common.action.remove')"
                            @click="remove(stage.id)"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
                <div v-if="editingIds.includes(stage.id)" class="p-3 border border-1 mt-2 rounded">
                    <StageForm
                        :id="stage.id"
                        :name="stage.name"
                        :short-name="stage.shortName"
                        :stage-groups="stage.groups"
                        @saved="edited"
                        @close="close"
                    ></StageForm>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.group-teams-count {
    font-size: 0.7rem !important;
}
</style>

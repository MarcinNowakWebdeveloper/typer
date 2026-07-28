<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useStageGroups } from '@/modules/admin/config/StagesPage/composables/useStageGroups.js'
import { useStageForm } from '@/modules/admin/config/StagesPage/composables/useStageForm.js'

const { t } = useI18n()
const props = defineProps({
    id: {
        type: Number,
        default: null,
    },
    name: {
        type: String,
        default: '',
    },
    shortName: {
        type: String,
        default: '',
    },
    stageGroups: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['saved', 'close'])
const groupInput = ref(null)

const {
    groupsLoading,
    selectedGroups,
    selectedGroupIds,
    filteredGroups,
    groupSearch,
    autocompleteOpen,
    loadGroups,
    addGroup,
    removeGroup,
} = useStageGroups(props.stageGroups, groupInput)

const { form, loading, submit, close } = useStageForm(props, emit, selectedGroupIds, selectedGroups, groupSearch)

onMounted(loadGroups)
</script>

<template>
    <div class="card p-3 group-edit">
        <div class="row mb-4">
            <h4 class="col">
                {{
                    !props.id ? t('admin.config.stagePage.form.title.new') : t('admin.config.stagePage.form.title.edit')
                }}
            </h4>

            <button v-if="props.id" class="btn btn-light col-auto me-3" style="float: right" @click="close()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form @submit.prevent="submit">
            <div class="mb-3 row">
                <div class="col-12 col-lg-6 mb-3">
                    <label v-if="props.id" for="name" class="form-label">{{
                        t('admin.config.stagePage.form.placeholder.teamSearch')
                    }}</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        :placeholder="t('admin.config.stagePage.form.placeholder.teamSearch')"
                        required
                    />
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label v-if="props.id" for="short_name" class="form-label">{{
                        t('admin.config.stagePage.form.placeholder.shortName')
                    }}</label>
                    <input
                        id="short_name"
                        v-model="form.shortName"
                        type="text"
                        class="form-control"
                        :placeholder="t('admin.config.stagePage.form.placeholder.shortName')"
                        required
                    />
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <div class="autocomplete">
                        <input
                            ref="groupInput"
                            v-model="groupSearch"
                            class="form-control"
                            :placeholder="t('admin.config.stagePage.form.placeholder.groupSearch')"
                            :disabled="groupsLoading"
                            @focus="autocompleteOpen = true"
                            @blur="autocompleteOpen = false"
                            @keydown.enter.prevent="addGroup(filteredGroups[0]?.id)"
                        />

                        <div v-if="autocompleteOpen && filteredGroups.length" class="autocomplete-options">
                            <button
                                v-for="group in filteredGroups"
                                :key="group.id"
                                type="button"
                                class="autocomplete-option"
                                @mousedown.prevent="addGroup(group.id)"
                            >
                                <span>{{ group.name }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-auto">
                    <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                        <i :class="props.id ? 'bi bi-floppy' : 'bi bi-plus-lg'"></i>
                        {{ loading ? t('common.saving') : t('common.action.save') }}
                    </button>
                </div>
            </div>

            <div v-if="selectedGroups.length" class="selected-group-list">
                <button
                    v-for="group in selectedGroups"
                    :key="group.id"
                    type="button"
                    class="selected-group"
                    :title="group.name"
                    @click="removeGroup(group.id)"
                >
                    <span>{{ group.name }}</span
                    ><br />
                    <span class="group-teams-count">
                        {{ group.teams.length }}
                        {{ t('admin.config.stagePage.stageGroup.teams') }}
                    </span>
                    <span class="remove-mark">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</template>

<style lang="scss" scoped>
.group-edit.card {
    border-color: rgba(var(--primary-rgb), 0.5);
    background-color: rgba(var(--primary-rgb), 0.15);

    input {
        border-color: rgba(var(--primary-rgb), 0.5);
        background-color: transparent;
    }

    .autocomplete {
        position: relative;
        display: flex;
        gap: 0.5rem;
    }

    .autocomplete-add {
        flex: 0 0 auto;
    }

    .autocomplete-options {
        position: absolute;
        top: calc(100% + 0.25rem);
        right: 3rem;
        left: 0;
        z-index: 20;
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid rgba(var(--primary-rgb), 0.35);
        border-radius: 0.375rem;
        background: #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
    }

    .autocomplete-option {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: 0;
        background: transparent;
        text-align: left;

        &:hover,
        &:focus {
            background-color: rgba(var(--primary-rgb), 0.12);
        }
    }

    .selected-group-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding-top: 0.5rem;
    }

    .selected-group {
        position: relative;
        height: 56px;
        padding: 0.35rem;
        border: 1px solid rgba(var(--primary-rgb), 0.35);
        border-radius: 0.375rem;
        background: #fff;

        img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .remove-mark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: inherit;
            color: #fff;
            background-color: rgba(220, 53, 69, 0.68);
            font-size: 1.4rem;
            opacity: 0;
            transition: opacity 0.15s ease-in-out;
        }

        &:hover .remove-mark,
        &:focus .remove-mark {
            opacity: 1;
        }
    }
}
</style>

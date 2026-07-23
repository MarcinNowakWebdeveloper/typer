<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useGroupTeams } from '@/modules/admin/config/GroupsPage/composables/useGroupTeams.js'
import { useGroupForm } from '@/modules/admin/config/GroupsPage/composables/useGroupForm.js'
import { useTeams } from '@/composables/useTeams.js'
import AppLoader from '@/components/loaders/AppLoader.vue'

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
    groupTeams: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['saved', 'close'])

const { loadTeams, teams, teamsLoading } = useTeams()

const { selectedTeams, selectedTeamIds, filteredTeams, teamSearch, autocompleteOpen, addTeam, removeTeam } =
    useGroupTeams(props.groupTeams, teams)

const { form, loading, submit, close } = useGroupForm(props, emit, selectedTeamIds, selectedTeams, teamSearch)

onMounted(loadTeams)
</script>

<template>
    <AppLoader v-if="teamsLoading" />
    <div v-else class="card p-3 group-edit">
        <div class="row mb-4">
            <h4 class="col">
                {{
                    !props.id ? t('admin.config.groupPage.form.title.new') : t('admin.config.groupPage.form.title.edit')
                }}
            </h4>

            <button v-if="props.id" class="btn btn-light col-auto me-3" @click="close()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form @submit.prevent="submit">
            <div class="row mb-3">
                <div class="col-12 col-lg-auto mb-3">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        :placeholder="t('admin.config.groupPage.form.placeholder.name')"
                        required
                    />
                </div>
                <div class="col-12 col-lg-auto d-flex align-items-center gap-2 mb-3">
                    <div class="autocomplete">
                        <input
                            v-model="teamSearch"
                            class="form-control"
                            :placeholder="t('admin.config.groupPage.form.placeholder.teamSearch')"
                            :disabled="teamsLoading"
                            @focus="autocompleteOpen = true"
                            @blur="autocompleteOpen = false"
                            @keydown.enter.prevent="addTeam(filteredTeams[0]?.id)"
                        />

                        <div v-if="autocompleteOpen && filteredTeams.length" class="autocomplete-options">
                            <button
                                v-for="team in filteredTeams"
                                :key="team.id"
                                type="button"
                                class="autocomplete-option"
                                @mousedown.prevent="addTeam(team.id)"
                            >
                                <img v-if="team.logo" :src="`/api/file/${team.logo.id}`" :alt="team.name" />
                                <span>{{ team.name }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-auto">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <i :class="props.id ? 'bi bi-floppy' : 'bi bi-plus-lg'"></i>
                        {{ loading ? t('common.saving') : t('common.action.save') }}
                    </button>
                </div>
            </div>

            <div v-if="selectedTeams.length" class="selected-team-logos">
                <button
                    v-for="team in selectedTeams"
                    :key="team.id"
                    type="button"
                    class="selected-team-logo"
                    :title="team.name"
                    @click="removeTeam(team.id)"
                >
                    <img v-if="team.logo" :src="`/api/file/${team.logo.id}`" :alt="team.name" />
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
        width: 100%;
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

        img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }
    }

    .selected-team-logos {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding-top: 0.5rem;
    }

    .selected-team-logo {
        position: relative;
        width: 56px;
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

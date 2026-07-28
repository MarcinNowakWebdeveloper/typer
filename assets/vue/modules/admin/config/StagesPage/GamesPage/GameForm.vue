<script setup>
import { useI18n } from 'vue-i18n'
import TeamAutocomplete from '@/components/teams/TeamAutocomplete.vue'
import { useGameForm } from '@/modules/admin/config/StagesPage/GamesPage/composables/useGameForm.js'

const props = defineProps({
    stageGroup: {
        type: Object,
        required: true,
    },
    game: {
        type: Object,
        required: false,
        default: null,
    },
})

const { t } = useI18n()
const emit = defineEmits(['saved', 'close'])

const { loading, form, submit, close } = useGameForm(props, emit)
</script>
<template>
    <div class="card p-3 group-edit">
        <div class="row mb-4">
            <h4 class="col">
                {{
                    !props.game?.id
                        ? t('admin.config.game.form.title.new', {
                              groupName: props.stageGroup.group.name,
                              stageName: props.stageGroup.stage.name,
                          })
                        : t('admin.config.game.form.title.edit', {
                              groupName: props.stageGroup.group.name,
                              stageName: props.stageGroup.stage.name,
                          })
                }}
            </h4>

            <button v-if="props.game?.id" class="btn btn-light col-auto me-3" @click="close()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form @submit.prevent="submit">
            <div class="mb-3 row align-items-start">
                <div class="mt-3 col-lg-6">
                    <label for="homeTeam" class="form-label">{{ t('admin.config.game.form.fields.homeTeam') }}</label>
                    <TeamAutocomplete
                        v-model="form.homeTeam"
                        :teams="props.stageGroup.group.teams"
                        :excluded-team-id="form.awayTeam"
                    />
                </div>

                <div class="mt-3 col-lg-6">
                    <label for="awayTeam" class="form-label">{{ t('admin.config.game.form.fields.awayTeam') }}</label>
                    <TeamAutocomplete
                        v-model="form.awayTeam"
                        :teams="props.stageGroup.group.teams"
                        :excluded-team-id="form.homeTeam"
                    />
                </div>

                <div class="mt-3 col-lg-6">
                    <label for="date" class="form-label">{{ t('admin.config.game.form.fields.date') }}</label>
                    <input id="date" v-model="form.date" type="date" class="form-control" required />
                </div>

                <div class="mt-3 col-lg-6">
                    <label for="time" class="form-label">{{ t('admin.config.game.form.fields.time') }}</label>
                    <input id="time" v-model="form.time" type="time" class="form-control" required />
                </div>

                <div class="mt-3 col-lg-6">
                    <label for="homeGoals" class="form-label">{{ t('admin.config.game.form.fields.homeGoals') }}</label>
                    <input id="homeGoals" v-model="form.homeGoals" type="number" class="form-control" />
                </div>

                <div class="mt-3 col-lg-6">
                    <label for="awayGoals" class="form-label">{{ t('admin.config.game.form.fields.homeGoals') }}</label>
                    <input id="awayGoals" v-model="form.awayGoals" type="number" class="form-control" />
                </div>

                <div class="mt-3 col-lg-12">
                    <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                        <i :class="props.game?.id ? 'bi bi-floppy' : 'bi bi-plus-lg'"></i>
                        {{ loading ? t('common.saving') : t('common.action.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

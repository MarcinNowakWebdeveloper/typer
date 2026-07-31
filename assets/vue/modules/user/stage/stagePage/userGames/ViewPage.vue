<script setup>
import { useI18n } from 'vue-i18n'

const props = defineProps({
    game: {
        type: Object,
        required: true,
    },
    homeGoals: {
        type: Number,
        default: null,
    },
    awayGoals: {
        type: Number,
        default: null,
    },
})

const { t } = useI18n()
</script>
<template>
    <div class="row m-3">
        <div class="col-6">
            {{ props.game.group_name }}
        </div>
        <div class="col-6 text-end">{{ props.game.date }} {{ props.game.time }}</div>
        <div class="row">
            <div class="col-12">
                <div class="d-grid align-items-center game-description">
                    <div class="text-end fw-bold">
                        <span class="d-none d-lg-inline">{{ props.game.home_team.name }}</span>
                        <img
                            v-tooltip
                            :src="`/api/file/${game.home_team.logo.id}`"
                            :alt="props.game.home_team.name"
                            class="team-logo mx-2"
                            loading="lazy"
                            :title="props.game.home_team.name"
                        />
                        {{ props.game.home_team.goals }}
                    </div>
                    <div class="px-3 fw-bold text-secondary">VS</div>
                    <div class="text-start fw-bold">
                        {{ props.game.away_team.goals }}
                        <img
                            v-tooltip
                            :src="`/api/file/${game.away_team.logo.id}`"
                            :alt="props.game.away_team.name"
                            class="team-logo mx-2"
                            loading="lazy"
                            :title="props.game.away_team.name"
                        />
                        <span class="d-none d-lg-inline">{{ props.game.away_team.name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="d-grid align-items-center game-description fw-light">
                <div class="text-end">
                    <span class="me-2 f08">{{ t('user.stage.game.betResult') }}:</span>
                    {{ props.homeGoals }}
                </div>
                <div class="px-3 fw-bold text-secondary">:</div>
                <div class="text-start">
                    {{ props.awayGoals }}
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.game-view {
    .game-description {
        grid-template-columns: 1fr auto 1fr;

        .team-logo {
            border: 1px solid #eeedf1;
            width: 40px;
        }
    }
}
</style>

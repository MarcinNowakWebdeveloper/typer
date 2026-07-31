<script setup>
import Countdown from '@/components/countdown/AppCountdown.vue'
import { useI18n } from 'vue-i18n'
import { useSuccessAnimation } from '@/modules/user/stage/stagePage/userGames/composables/useSuccessAnimation.js'
import { usePrediction } from '@/modules/user/stage/stagePage/userGames/composables/usePrediction.js'

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
const startDate = props.game.date + ' ' + props.game.time

const { saved, showSuccess } = useSuccessAnimation()

const { loading, homeGoals, awayGoals, submit } = usePrediction(
    props.game.id,
    props.homeGoals,
    props.awayGoals,
    showSuccess,
)
</script>
<template>
    <div class="row m-3">
        <div class="col col-6">
            {{ game.group_name }}
        </div>
        <div class="col col-6 text-end">
            <span><Countdown :date="startDate" variant="less_than_day" /></span>
        </div>
        <div class="col col-12">
            <div class="game-description align-items-center">
                <div class="text-end fw-bold home_team mt-2">
                    {{ game.home_team.name }}
                    <img
                        :src="`/api/file/${game.home_team.logo.id}`"
                        :alt="game.home_team.logo.originName"
                        class="team-logo ms-2"
                        loading="lazy"
                    />
                </div>

                <div class="fw-bold text-secondary goals mt-2">
                    <input v-model="homeGoals" type="number" min="0" class="form-control ms-3" />
                    <span class="px-3">VS</span>
                    <input v-model="awayGoals" type="number" min="0" class="form-control me-3" />
                </div>

                <div class="text-start fw-bold away_team mt-2">
                    <img
                        :src="`/api/file/${game.away_team.logo.id}`"
                        :alt="game.away_team.logo.originName"
                        class="team-logo me-2"
                        loading="lazy"
                    />
                    {{ game.away_team.name }}
                </div>
            </div>
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary col-auto col-lg-2" :disabled="loading" @click="submit">
                    <i class="bi bi-floppy me-1"></i>
                    {{ loading ? t('common.saving') : t('common.action.save') }}
                </button>
            </div>
        </div>
    </div>

    <div v-if="saved" class="success-overlay">
        <div class="success-message">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ t('common.saved') }}
        </div>
    </div>
</template>
<style lang="scss">
.game-edit {
    position: relative;

    .game-description {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;

        .home_team {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.45rem;
        }

        input {
            width: 70px;
            display: inline;
        }

        .team-logo {
            border: 1px solid #eeedf1;
            width: 40px;
        }
    }

    @media (max-width: 767.98px) {
        .game-description {
            grid-template-columns: 1fr 50px 1fr;
            grid-template-areas:
                'left . right'
                'center center center';

            .home_team {
                grid-area: left;
            }

            .goals {
                grid-area: center;
                justify-self: center;
            }

            .away_team {
                grid-area: right;
            }
        }
    }
}

.success-overlay {
    position: absolute;
    inset: 0;

    display: flex;
    justify-content: center;
    align-items: center;

    background: rgba(25, 135, 84, 0.9);
    z-index: 1000;

    animation: fadeOut 3s forwards;

    .success-message {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }

    to {
        opacity: 0;
    }
}
//}
</style>

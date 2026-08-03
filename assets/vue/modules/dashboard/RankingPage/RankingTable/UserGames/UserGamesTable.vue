<script setup>
import { useI18n } from 'vue-i18n'

const props = defineProps({
    stage: {
        type: Object,
        required: true,
    },
    maxPoints: {
        type: Number,
        required: true,
    },
})

const { t } = useI18n()

function getPointsColor(index) {
    const ratio = index / (props.maxPoints - 1)

    const red = Math.round(255 * (1 - ratio))
    const green = Math.round(255 * ratio)

    return `rgba(${red}, ${green}, 0, 0.25)`
}

const isLast24Hours = (date) => {
    const now = Date.now()
    const given = new Date(date).getTime()

    return now - given <= 24 * 60 * 60 * 1000
}
</script>
<template>
    <table v-if="props.stage.games.length > 0" class="table table-striped ranking-table">
        <thead>
            <tr>
                <th scope="col" class="f08">{{ t('dashboard.userGames.table.stage') }}</th>
                <th scope="col" class="f08">{{ t('dashboard.userGames.table.date') }}</th>
                <th scope="col" class="f08 text-center">{{ t('dashboard.userGames.table.game') }}</th>
                <th scope="col" class="f08 text-center">{{ t('dashboard.userGames.table.bet') }}</th>
                <th scope="col" class="f08 text-center">{{ t('dashboard.userGames.table.score') }}</th>
                <th scope="col" class="f08 text-center">{{ t('dashboard.userGames.table.points') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="userGame in props.stage.games"
                :key="userGame.id"
                :class="['f08 fw-bold', isLast24Hours(userGame.game.date) ? 'isLast24Hours' : '']"
            >
                <td class="align-middle">
                    <span class="stage rounded px-2 py-1">{{ props.stage.short_name }}</span>
                </td>
                <td>
                    {{ new Date(userGame.game.date).toLocaleDateString('pl-PL') }}
                </td>
                <td>
                    <div class="d-grid align-items-center teams">
                        <div class="text-end fw-bold">
                            <span class="d-none d-lg-inline me-2">
                                {{ userGame.game.homeTeam.name }}
                            </span>
                            <img
                                v-tooltip
                                :src="`/api/file/${userGame.game.homeTeam.logo.id}`"
                                class="team-logo me-2"
                                :title="userGame.game.homeTeam.name"
                                loading="lazy"
                            />
                        </div>

                        <div class="px-2 fw-bold">VS</div>

                        <div class="text-start fw-bold">
                            <img
                                v-tooltip
                                :src="`/api/file/${userGame.game.awayTeam.logo.id}`"
                                class="team-logo ms-2"
                                :title="userGame.game.awayTeam.name"
                                loading="lazy"
                            />
                            <span class="d-none d-lg-inline ms-2">
                                {{ userGame.game.awayTeam.name }}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="align-middle text-center">
                    <span class="prediction bg-secondary-subtle rounded px-2 py-1">
                        {{ userGame.homeGoals }} : {{ userGame.awayGoals }}
                    </span>
                </td>
                <td class="align-middle text-center">{{ userGame.game.homeGoals }} : {{ userGame.game.awayGoals }}</td>
                <td class="text-center">
                    <span
                        class="prediction rounded px-2 py-1"
                        :style="{ backgroundColor: getPointsColor(userGame.points) }"
                    >
                        {{ userGame.points > 0 ? '+' : '' }} {{ userGame.points }}
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</template>
<style lang="scss">
.ranking-table {
    .user-games {
        max-width: 1000px;

        .content {
            table {
                margin-bottom: 0;

                .stage {
                    background: rgba(var(--primary-rgb), 0.15);
                    color: var(--primary);
                }

                tr.isLast24Hours > td {
                    background: rgba(var(--success-rgb), 0.1);
                    --bs-table-bg-type: unset;
                    --bs-table-hover-bg: unset;
                }

                td > .teams {
                    grid-template-columns: 1fr auto 1fr;
                }
            }
        }
    }
}
</style>

<script setup>
import UserAvatar from '@/components/users/UserAvatar.vue'
import TrendIcon from '@/../icons/trend.svg'
import { useI18n } from 'vue-i18n'
import UserGames from '@/modules/dashboard/RankingPage/RankingTable/UserGames.vue'
import { ref } from 'vue'

const { row } = defineProps({
    row: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()
const trophyColors = {
    1: 'gold',
    2: 'silver',
    3: 'bronze',
}

const getColor = (position) => trophyColors[position]
let open = ref(false)
</script>
<template>
    <tr>
        <td class="position text-center align-middle w-auto">
            <i :class="['bi bi-trophy-fill fw-bold', getColor(row.position)]"></i>
            {{ row.position }}
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <span v-tooltip :title="row.user.name">
                    <UserAvatar :name="row.user.name" :color="row.user.color" />
                </span>
                <span class="d-none d-lg-inline">
                    {{ row.user.name }}
                </span>
            </div>
        </td>
        <td class="text-center align-middle">
            <span v-if="row.positionChange < 0" class="position-change position-up">
                <TrendIcon />
                +{{ Math.abs(row.positionChange) }}
            </span>
            <span v-else-if="row.positionChange > 0" class="position-change position-down">
                <TrendIcon class="icon-down" />
                -{{ Math.abs(row.positionChange) }}
            </span>
            <span v-else class="position-change position-same">
                <i class="bi bi-dash"></i>
            </span>
        </td>
        <td class="text-center align-middle">
            <span
                :class="[
                    'today rounded px-2 py-1 fw-bold f08',
                    row.todayPoints > 0 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary',
                ]"
            >
                {{ row.todayPoints > 0 ? '+' : '' }}&nbsp;{{ row.todayPoints }}
            </span>
        </td>
        <td class="text-center align-middle">
            <span class="fw-bold me-1">{{ row.points }}</span>
            <span class="pts f08">{{ t('dashboard.ranking.table.pts') }}.</span>
        </td>
        <td class="align-middle text-center text-lg-start">
            <template v-if="row.joker?.team_name">
                <img
                    v-if="row.joker.team_logo"
                    v-tooltip
                    :src="`/api/file/${row.joker.team_logo}`"
                    class="group-team-logo me-2"
                    loading="lazy"
                    :title="row.joker.team_name"
                />
                <span class="d-none d-lg-inline">
                    {{ row.joker.team_name }}
                </span>
            </template>
            <i v-else class="bi bi-dash"></i>
        </td>
        <td v-if="open" class="text-center align-middle" @click="open = false">
            <i class="bi bi-chevron-compact-down"></i>
        </td>
        <td v-else class="text-center align-middle" @click="open = true">
            <i class="bi bi-chevron-compact-up"></i>
        </td>
    </tr>
    <tr v-if="open">
        <td colspan="7" class="p-0">
            <UserGames :user-games="row.stages" :max-points="row.maxPoints" />
        </td>
    </tr>
</template>
<style lang="scss">
table.ranking-table tbody {
    .position {
        width: 1% !important;

        i {
            color: #dee2e6;

            &.gold {
                color: var(--gold);
            }

            &.silver {
                color: var(--silver);
            }

            &.bronze {
                color: var(--bronze);
            }
        }
    }

    .position-change {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: bold;
    }

    .pts {
        color: #6c757d;
    }

    .group-team-logo {
        width: 24px;
    }

    .position-up {
        color: #28a745;
    }

    .position-down {
        color: #dc3545;
    }

    .position-same {
        color: #6c757d;
    }

    .icon-down {
        transform: scaleY(-1);
    }
}
</style>

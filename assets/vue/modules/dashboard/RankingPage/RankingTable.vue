<script setup>
import { useI18n } from 'vue-i18n'
import RankingTableRow from '@/modules/dashboard/RankingPage/RankingTable/RankingTableRow.vue'
import { useRankingTable } from '@/modules/dashboard/RankingPage/composables/useRankingTable.js'

const { ranking } = defineProps({
    ranking: {
        type: Array,
        required: true,
    },
})

const { t } = useI18n()
const { sortedRanking, handleSort, sortDirection, sortColumn } = useRankingTable(ranking)
</script>
<template>
    <div v-if="sortedRanking" class="table-responsive mt-3">
        <table class="table table-hover ranking-table">
            <thead>
                <tr>
                    <th class="sortable w-auto" @click="handleSort('position')">
                        {{ t('dashboard.ranking.table.position') }}
                        <i
                            v-if="sortColumn === 'position'"
                            :class="`bi bi-${sortDirection === 'asc' ? 'arrow-up' : 'arrow-down'}`"
                        ></i>
                    </th>
                    <th>{{ t('dashboard.ranking.table.player') }}</th>
                    <th class="sortable text-center align-middle" @click="handleSort('changeOfPosition')">
                        {{ t('dashboard.ranking.table.changeOfPosition') }}
                        <i
                            v-if="sortColumn === 'changeOfPosition'"
                            :class="`bi bi-${sortDirection === 'asc' ? 'arrow-up' : 'arrow-down'}`"
                        ></i>
                    </th>
                    <th class="sortable text-center align-middle" @click="handleSort('todayPoints')">
                        {{ '<24h' }}
                        <i
                            v-if="sortColumn === 'todayPoints'"
                            :class="`bi bi-${sortDirection === 'asc' ? 'arrow-up' : 'arrow-down'}`"
                        ></i>
                    </th>
                    <th class="text-center">{{ t('dashboard.ranking.table.sum') }}</th>
                    <th>{{ t('dashboard.ranking.table.joker') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <template v-for="row in sortedRanking" :key="row.user.id">
                    <RankingTableRow :row="row" />
                </template>
            </tbody>
        </table>
        <div class="flex flex-wrap description">
            <span class="me-2">
                <span class="fw-bold text-success">
                    {{ t('dashboard.ranking.table.description.positionUp.title') }} ↑
                </span>
                — {{ t('dashboard.ranking.table.description.positionUp.description') }}
            </span>
            <span class="me-2">
                <span class="fw-bold text-danger">
                    {{ t('dashboard.ranking.table.description.positionDown.title') }} ↓
                </span>
                — {{ t('dashboard.ranking.table.description.positionDown.description') }}
            </span>
            <span class="me-2">
                <span class="fw-bold">
                    {{ t('dashboard.ranking.table.description.24h.title') }}
                </span>
                — {{ t('dashboard.ranking.table.description.24h.description') }}
            </span>
        </div>
    </div>
</template>
<style lang="scss">
.ranking {
    .description {
        font-size: 0.8em;
    }

    table.ranking-table {
        @media (min-width: 992px) {
            min-width: 700px;
        }

        th {
            white-space: nowrap;

            &.sortable {
                cursor: pointer;
                user-select: none;

                &:hover {
                    background-color: rgba(0, 0, 0, 0.05);
                }
            }
        }
    }
}
</style>

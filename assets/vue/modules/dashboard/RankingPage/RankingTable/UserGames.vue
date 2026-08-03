<script setup>
import Table from '@/modules/dashboard/RankingPage/RankingTable/UserGames/UserGamesTable.vue'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    userGames: {
        type: Array,
        required: true,
    },
    maxPoints: {
        type: Number,
        required: true,
    },
})

const { t } = useI18n()
const visibleStage = ref(null)

for (let i = 0; i < props.userGames.length; i++) {
    if (props.userGames[i].games.length > 0) {
        visibleStage.value = i
        break
    }
}

const availableStages = computed(() =>
    props.userGames
        .map((stage, index) => ({
            index,
            short_name: stage.short_name,
            games: stage.games,
        }))
        .filter((stage) => stage.games.length > 0)
        .reverse(),
)
</script>
<template>
    <div class="user-games mx-auto mt-1 mb-3">
        <div class="header px-3 py-2 d-flex align-items-center justify-content-between">
            <div>{{ t('dashboard.userGames.pointsHistory') }}</div>
            <div class="d-none d-md-flex align-items-center gap-2">
                <select v-model="visibleStage" class="form-select form-select-sm w-auto">
                    <option v-for="stage in availableStages" :key="stage.short_name" :value="stage.index">
                        {{ stage.short_name }}
                    </option>
                </select>
            </div>
            <div class="d-md-none">
                <select v-model="visibleStage" class="form-select form-select-sm">
                    <option v-for="stage in availableStages" :key="stage.short_name" :value="stage.index">
                        {{ stage.short_name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="content">
            <template v-for="(stage, index) in props.userGames" :key="stage.short_name">
                <Table v-if="index === visibleStage" :stage="stage" :max-points="props.maxPoints" />
            </template>
        </div>
        <div class="bottom px-3 py-2"></div>
    </div>
</template>
<style lang="scss">
.ranking-table {
    .user-games {
        .header {
            color: white;
            background: var(--primary);
            border-radius: 13px 13px 0 0;
        }

        .content {
            border: solid var(--primary);
            border-width: 0 1px;
        }

        .bottom {
            background: var(--light);
            border-radius: 0 0 13px 13px;
            border: solid var(--primary);
            border-width: 0 1px 1px 1px;
            padding-bottom: 5px;

            .stage-tab {
                cursor: pointer;
                border-radius: 0 0 13px 13px;
                border: solid var(--primary);
                border-width: 0 1px 1px 1px;
                margin-left: 5px;
            }
        }
    }
}
</style>

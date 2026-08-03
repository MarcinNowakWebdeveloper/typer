<script setup>
import UserAvatar from '@/components/users/UserAvatar.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    firstPoints: {
        type: Number,
        required: true,
    },
    order: {
        type: Number,
        required: true,
    },
    item: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()
const maxHeight = 160
const height = computed(() => Math.round((props.item.points / props.firstPoints) * maxHeight))
</script>
<template>
    <div :class="['podium-item', 'order-' + props.order]">
        <UserAvatar :name="props.item.userName" :color="props.item.userColor" />
        <div class="name">{{ props.item.userName }}</div>
        <div class="points">{{ props.item.points }} {{ t('dashboard.ranking.table.pts') }}</div>

        <div :class="['bar', props.item.color]" :style="{ height: height + 'px' }">
            <div class="place">#{{ props.item.position }}</div>
        </div>
    </div>
</template>
<style lang="scss">
.podium {
    .podium-content {
        .podium-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 140px;

            @media (max-width: 767.98px) {
                min-width: 100px;
            }

            @media (max-width: 460px) {
                min-width: 70px;
            }

            &.order-1 {
                order: 2 !important;
            }

            &.order-2 {
                order: 1 !important;
            }

            &.order-3 {
                order: 3 !important;
            }

            .bar {
                width: 80px;
                border-radius: 10px 10px 0 0;
                display: flex;
                justify-content: center;
                align-items: flex-end;

                &.gold {
                    background-color: var(--gold);
                }

                &.silver {
                    background-color: var(--silver);
                }

                &.bronze {
                    background-color: var(--bronze);
                }

                .place {
                    color: white;
                    font-size: 28px;
                    font-weight: bold;
                    margin-bottom: 12px;
                }
            }

            .name {
                font-weight: 600;
                text-align: center;
            }

            .points {
                margin-top: 6px;
                margin-bottom: 12px;
                font-weight: 700;
            }
        }
    }
}
</style>

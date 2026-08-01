<script setup>
import UserAvatar from '@/components/users/UserAvatar.vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    predictions: {
        type: Object,
        required: true,
    },
    maxPoints: {
        type: Number,
        required: true,
    },
    pointsSet: {
        type: Boolean,
        required: true,
    },
})

const { t } = useI18n()
const auth = useAuthStore()

function getPointsColor(index) {
    const ratio = index / (props.maxPoints - 1)

    const red = Math.round(255 * (1 - ratio))
    const green = Math.round(255 * ratio)

    return `rgba(${red}, ${green}, 0, 0.25)`
}
</script>
<template>
    <div class="predictions py-3 px-4">
        <div v-for="prediction in props.predictions" :key="prediction" class="prediction my-2">
            <div class="d-flex align-items-center gap-2">
                <div
                    class="d-flex align-items-center justify-content-center rounded bg-secondary-subtle px-2 py-1 text-nowrap fw-bold"
                >
                    {{ prediction.prediction }}
                </div>
                <div
                    v-if="pointsSet"
                    class="d-flex align-items-center justify-content-center rounded px-2 py-1 text-nowrap fw-bold points"
                    :style="{ backgroundColor: getPointsColor(prediction.points - 1) }"
                    :title="t('user.stage.game.points')"
                >
                    +{{ prediction.points }}
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span v-for="(user, id) in prediction.users" :key="user.name" class="d-flex align-items-center gap-2">
                    <UserAvatar :name="user.name" :color="user.color"></UserAvatar>
                    <span :class="[auth.user.id === Number(id) ? 'fw-bold  bg-success-subtle py-1 px-2 rounded' : '']">
                        {{ user.name }}
                    </span>
                </span>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.game-view .predictions {
    .prediction {
        display: grid;
        grid-template-columns: max-content minmax(0, 1fr);
        gap: 0.5rem 1rem;
        border-bottom: 0;

        @media (max-width: 930px) {
            border-bottom: 1px solid rgba(var(--primary-rgb), 0.35);
            padding-bottom: 0.5rem;
        }

        .points {
            font-size: 0.8rem;
        }
    }
}
</style>

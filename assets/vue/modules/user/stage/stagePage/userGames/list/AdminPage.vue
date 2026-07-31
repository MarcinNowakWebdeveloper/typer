<script setup>
import UserAvatar from '@/components/users/UserAvatar.vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    predictions: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()
</script>
<template>
    <div class="admin-users-games py-3 px-2 px-xl-4">
        <div class="d-flex align-items-center didntBet">
            <div class="col-3 col-lg-1 me-1">{{ t('user.stage.games.admin.didntBet') }}:</div>
            <span class="col-9 col-lg-11 d-flex flex-wrap gap-2 align-items-center">
                <span
                    v-for="prediction in props.predictions.unset"
                    :key="prediction.name"
                    v-tooltip
                    :title="prediction.name"
                >
                    <UserAvatar :name="prediction.name" :color="prediction.color" />
                </span>
            </span>
        </div>
        <div class="d-flex mt-2 align-items-center">
            <div class="col-3 col-lg-1 me-1">{{ t('user.stage.games.admin.bet') }}:</div>
            <span class="col-9 col-lg-11 d-flex flex-wrap gap-2 align-items-center">
                <span
                    v-for="prediction in props.predictions.set"
                    :key="prediction.name"
                    v-tooltip
                    :title="prediction.name"
                >
                    <UserAvatar :name="prediction.name" :color="prediction.color" />
                </span>
            </span>
        </div>
    </div>
</template>
<style lang="scss">
.admin-users-games {
    .didntBet {
        @media (max-width: 930px) {
            border-bottom: 1px solid rgba(var(--primary-rgb), 0.35);
            padding-bottom: 0.5rem;
        }
    }
}
</style>

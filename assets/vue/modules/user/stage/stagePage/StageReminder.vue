<script setup>
import Countdown from '@/components/countdown/AppCountdown.vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    stage: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()

const editable = computed(() => {
    if (!props.stage.value?.start_date) {
        return false
    }

    return new Date(props.stage.value.start_date).getTime() > Date.now()
})

const formatGameDate = (dateString) => {
    const date = new Date(dateString)

    const weekday = new Intl.DateTimeFormat('pl-PL', {
        weekday: 'long',
    }).format(date)

    const dayMonth = new Intl.DateTimeFormat('pl-PL', {
        day: 'numeric',
        month: 'long',
    }).format(date)

    const time = new Intl.DateTimeFormat('pl-PL', {
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
    const capitalize = (text) => text.charAt(0).toUpperCase() + text.slice(1)

    return `${capitalize(weekday)} ${dayMonth} - ${time}`
}
</script>
<template>
    <div class="card reminder p-3">
        <div class="row align-items-start">
            <div class="col-6 col">
                <div><i class="bi bi-clock me-2"></i>{{ t('user.stage.reminder.start') }}</div>
                <h2>{{ formatGameDate(props.stage.start_date) }}</h2>
                <div>{{ props.stage.name }}</div>
            </div>
            <div v-if="editable" class="col-6 col">
                <div class="text-end">{{ t('user.stage.reminder.leftToStart') }}</div>
                <h2 class="text-end">
                    <Countdown :date="props.stage.start_date" variant="detailed" />
                </h2>
            </div>
            <div v-else class="col-6 col">
                <div class="text-end">{{ t('user.stage.reminder.leftToEnd') }}</div>
                <h2 class="text-end">
                    <Countdown :date="props.stage.end_date" variant="detailed" />
                </h2>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.reminder {
    background-image: linear-gradient(to right, oklch(0.637 0.237 25.331) 0%, oklch(0.705 0.213 47.604) 100%);
    color: white !important;
}
</style>

<script setup>
import { computed } from 'vue'
import { useCountdown } from '@/composables/useCountdown'

const { getCountdown } = useCountdown()

const props = defineProps({
    date: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'compact',
    },
})

const countdown = computed(() => getCountdown(props.date))
</script>

<template>
    <span v-if="variant === 'compact'"> {{ countdown.days }}d {{ countdown.hours }}h {{ countdown.minutes }}m </span>

    <div v-else-if="variant === 'detailed'">
        <span v-if="countdown.days > 0">{{ countdown.days }}&nbsp;dni </span>
        <span v-if="countdown.days > 0 || countdown.hours > 0"> {{ countdown.hours }}&nbsp;godzin </span>
        <span v-if="countdown.days > 0 || countdown.hours > 0 || countdown.minutes > 0">
            {{ countdown.minutes }}&nbsp;minut
        </span>
        <span v-if="countdown.days > 0 || countdown.hours > 0 || countdown.minutes > 0 || countdown.seconds > 0">
            {{ countdown.seconds }}&nbsp;sekund
        </span>
    </div>

    <div v-else-if="variant === 'less_than_day'">
        <span v-if="countdown.days > 0">{{ props.date }} </span>
        <span v-else>
            <span v-if="countdown.hours > 0"> {{ countdown.hours }}&nbsp;godzin </span>
            <span v-if="countdown.hours > 0 || countdown.minutes > 0"> {{ countdown.minutes }}&nbsp;minut </span>
            <span v-if="countdown.hours > 0 || countdown.minutes > 0 || countdown.seconds > 0">
                {{ countdown.seconds }}&nbsp;sekund
            </span>
        </span>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { getTextColor } from '@/composables/textColor.js'

const props = defineProps({
    name: {
        type: String,
        default: null,
    },
    color: {
        type: String,
        default: null,
    },
})

const initials = computed(() => {
    if (!props.name) {
        return ''
    }

    return props.name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .substring(0, 2)
        .toUpperCase()
})

const colorStyle = computed(() => {
    if (!props.name && !props.color) {
        return {
            backgroundColor: `hsl(0, 0%, 0%)`,
            color: `hsl(0, 0%, 0%)`,
        }
    }

    if (props.color) {
        return {
            backgroundColor: `rgba(${props.color}, 1)`,
            color: getTextColor(props.color),
        }
    }

    const hash = [...props.name].reduce((sum, char) => sum + char.charCodeAt(0), 0)

    const hue = hash % 360

    return {
        backgroundColor: `hsl(${hue}, 80%, 92%)`,
        color: `hsl(${hue}, 70%, 35%)`,
    }
})
</script>

<template>
    <div class="user-avatar" :style="colorStyle" :title="props.name">
        {{ initials }}
    </div>
</template>

<style scoped>
.user-avatar {
    width: 40px;
    height: 40px;
    flex-shrink: 0;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #eef2ff;
    color: #5b4cff;

    font-weight: 600;
}
</style>

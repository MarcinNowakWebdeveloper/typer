<script setup>
import UserAvatar from '@/components/users/UserAvatar.vue'

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['update:modelValue', 'highlight', 'downplay'])

const toggle = (user) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [user.userId]: !props.modelValue[user.userId],
    })
}
</script>

<template>
    <div class="points-line-chart-legend p-3">
        <div
            v-for="user in users"
            :key="user.userId"
            class="legend-item"
            :class="{ disabled: !modelValue[user.userId] }"
            @click="toggle(user)"
            @mouseenter="emit('highlight', user)"
            @mouseleave="emit('downplay', user)"
        >
            <UserAvatar :name="user.userName" :color="user.userColor" />
            <span>{{ user.userName }}</span>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.points-line-chart-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;

        &.disabled {
            opacity: 0.3;
        }
    }
}
</style>

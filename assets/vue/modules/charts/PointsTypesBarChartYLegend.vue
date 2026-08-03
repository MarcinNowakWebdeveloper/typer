<script setup>
import UserAvatar from '@/components/users/UserAvatar.vue'

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
})
</script>
<template>
    <div class="points-types-bar-chart-y-legend ps-3">
        <div v-for="user in [...props.users].reverse()" :key="user.userId" class="d-flex align-items-center gap-2 user">
            <UserAvatar :name="user.userName" :color="user.userColor" />
            {{ user.userName }}
        </div>
    </div>
</template>
<style lang="scss" scoped>
.points-types-bar-chart-y-legend {
    padding-top: 0.6rem;
    .user {
        height: 43px;

        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 13px;
        }
    }
}
</style>

<script setup>
const props = defineProps({
    stages: {
        type: Array,
        required: true,
    },
    stageId: {
        type: Number,
        required: true,
    },
})
</script>
<template>
    <div class="border-bottom px-4 pt-2 d-flex align-items-end stage-tabs">
        <router-link
            v-for="stageTab in stages"
            :key="stageTab.id"
            :to="{ path: `/stage/${stageTab.id}` }"
            class="nav-link"
        >
            <button
                :class="[
                    'py-2 px-4 stage-tab me-2 text-secondary fw-bolder',
                    stageTab.id === props.stageId ? 'active' : '',
                ]"
            >
                {{ stageTab.short_name }}
            </button>
        </router-link>
    </div>
</template>
<style lang="scss" scoped>
.stage-tabs {
    display: flex;
    align-items: flex-end;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;

    .nav-link {
        flex-shrink: 0;
    }
}
</style>

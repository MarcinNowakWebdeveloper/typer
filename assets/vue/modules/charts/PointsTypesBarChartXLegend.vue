<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
    pointTypes: {
        type: Array,
        required: true,
    },
})

const emit = defineEmits(['update:visibleTypes'])

const visibleTypes = ref({})

onMounted(() => {
    const initial = {}
    props.pointTypes.forEach((type) => {
        initial[type.key] = true
    })
    visibleTypes.value = initial
    emit('update:visibleTypes', visibleTypes.value)
})

const toggleType = (type) => {
    visibleTypes.value[type.key] = !visibleTypes.value[type.key]
    emit('update:visibleTypes', visibleTypes.value)
}
</script>
<template>
    <div class="points-types-bar-chart-x-legend p-3 m-auto">
        <div
            v-for="type in pointTypes"
            :key="type.key"
            class="legend-item"
            :class="{
                disabled: !visibleTypes[type.key],
            }"
            @click="toggleType(type)"
        >
            <div class="legend-color" :style="{ background: type.color }" />
            <span>
                {{ type.label }}
            </span>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.points-types-bar-chart-x-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;

        &.disabled {
            opacity: 0.3;
        }

        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 50%;
        }
    }
}
</style>

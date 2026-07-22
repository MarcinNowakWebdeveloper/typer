<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: Number,
        required: true,
    },
    variant: {
        type: String,
        required: true,
    },
    icon: {
        type: String,
        default: '',
    },
})
</script>

<template>
    <div class="stat-card" :class="variant">
        <div class="stat-title d-flex align-items-center justify-content-center">
            <i v-tooltip :class="['bi', icon]" :title="title" />
            <span class="d-inline d-md-none d-lg-inline ms-2 fw-bold">{{ title }}</span>
        </div>

        <div class="stat-value text-center">
            <h2>{{ value }}</h2>
        </div>
    </div>
</template>

<style lang="scss">
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #eee;

    &.primary {
        border-color: rgba(var(--primary-rgb), 0.5);
        background-color: rgba(var(--primary-rgb), 0.15);
        i {
            color: var(--primary);
        }
    }

    &.success {
        border-color: rgba(var(--success-rgb), 0.5);
        background-color: rgba(var(--success-rgb), 0.15);
        i {
            color: var(--success);
        }
    }

    &.danger {
        border-color: rgba(var(--danger-rgb), 0.5);
        background-color: rgba(var(--danger-rgb), 0.15);
        i {
            color: var(--danger);
        }
    }

    &.warning {
        border-color: rgba(var(--warning-rgb), 0.5);
        background-color: rgba(var(--warning-rgb), 0.15);
        i {
            color: var(--warning);
        }
    }

    i {
        font-size: 24px;
    }
}
</style>

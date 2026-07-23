<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
<template>
    <div class="text-center p-4">
        <div class="spinner-border" role="status"></div>
        <div>{{ t('common.loading') }}</div>
    </div>
</template>

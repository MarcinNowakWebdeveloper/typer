<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
<template>
    <div class="no-date card text-center py-4">
        <h1><i class="bi bi-clock"></i> {{ t('common.noData') }}</h1>
    </div>
</template>

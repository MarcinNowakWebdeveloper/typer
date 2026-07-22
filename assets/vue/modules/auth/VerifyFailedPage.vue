<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
<template>
    <div class="card">
        <h1 class="mb-4">
            <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i>{{ t('auth.verifyFailedPage.title') }}
        </h1>

        <p>{{ t('auth.verifyFailedPage.message') }}</p>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
<template>
    <div class="card">
        <h1 class="mb-4">
            <i class="bi bi-check-circle-fill text-success me-2"></i>{{ t('auth.waitingForActivationPage.title') }}
        </h1>

        <p>{{ t('auth.waitingForActivationPage.message') }}</p>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
<template>
    <div class="card">
        <h1 class="mb-4"><i class="bi bi-gear color-primary pe-2" />{{ t('admin.config.configPage.title') }}</h1>

        <div class="mb-3">
            <nav class="nav nav-tabs">
                <router-link :to="{ name: 'config_teams' }" class="nav-link" active-class="active">
                    {{ t('admin.config.configPage.teams') }}
                </router-link>
                <router-link :to="{ name: 'config_groups' }" class="nav-link" active-class="active">
                    {{ t('admin.config.configPage.groups') }}
                </router-link>
            </nav>
        </div>
        <div>
            <router-view />
        </div>
    </div>
</template>

<style lang="scss">
.card {
    .nav {
        .nav-link {
            &.active {
                color: var(--text-primary);
            }

            color: var(--default);
        }
    }
}
</style>

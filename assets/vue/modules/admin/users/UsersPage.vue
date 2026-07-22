<script setup>
import { onMounted } from 'vue'
import StatCard from '@/components/cards/StatCard.vue'
import { useUsers } from '@/modules/admin/users/composables/useUsers.js'
import { useUserStats } from '@/modules/admin/users/composables/useUserStats.js'
import { useI18n } from 'vue-i18n'
import UserTable from '@/modules/admin/users/UsersPage/UserTable.vue'
import { useUserAdministration } from '@/modules/admin/users/composables/useUserAdministration.js'

const { t } = useI18n()

const { users, page, status, pagination, loadUsers, changeStatus, nextPage, previousPage, userFilters } = useUsers()

const { stats, loadStats } = useUserStats()

const { activate, deactivate } = useUserAdministration(loadUsers, loadStats)

onMounted(() => {
    loadUsers()
    loadStats()
})
</script>

<template>
    <div class="card">
        <h1 class="mb-4">
            <i class="bi bi-shield color-primary" />
            {{ t('admin.users.title') }}
        </h1>

        <div v-if="stats" class="row g-3 mb-4">
            <div class="col-md-3">
                <StatCard
                    :title="t('admin.users.stats.totals')"
                    :value="stats.total"
                    variant="primary"
                    icon="bi-people"
                />
            </div>

            <div class="col-md-3">
                <StatCard
                    :title="t('admin.users.stats.active')"
                    :value="stats.active"
                    variant="success"
                    icon="bi-check-circle"
                />
            </div>

            <div class="col-md-3">
                <StatCard
                    :title="t('admin.users.stats.inactive')"
                    :value="stats.inactive"
                    variant="danger"
                    icon="bi-x-circle"
                />
            </div>

            <div class="col-md-3">
                <StatCard
                    :title="t('admin.users.stats.unconfirmed')"
                    :value="stats.unconfirmed"
                    variant="warning"
                    icon="bi-envelope"
                />
            </div>
        </div>

        <div class="filters">
            <button
                v-for="filter in userFilters"
                :key="filter.value"
                :class="['btn me-2 mb-2', status === filter.value ? 'btn-primary' : 'btn-outline-secondary']"
                @click="changeStatus(filter.value)"
            >
                {{ t(filter.label) }}
            </button>
        </div>

        <UserTable :users="users" @deactivate="deactivate" @activate="activate" />

        <div class="d-flex gap-2 align-items-center">
            <button class="btn me-2 btn-outline-secondary" :disabled="page <= 1" @click="previousPage">
                {{ t('common.table.previous') }}
            </button>

            <span>
                {{ pagination?.pages >= 1 ? page : 0 }}
                /
                {{ pagination?.pages }}
            </span>

            <button class="btn me-2 btn-outline-secondary" :disabled="page >= pagination?.pages" @click="nextPage">
                {{ t('common.table.next') }}
            </button>
        </div>
    </div>
</template>

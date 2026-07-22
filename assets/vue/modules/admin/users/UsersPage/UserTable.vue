<script setup>
import UserAvatar from '@/components/users/UserAvatar.vue'
import StatusBadge from '@/components/badges/StatusBadge.vue'
import { useI18n } from 'vue-i18n'

defineProps({
    users: {
        type: Array,
        required: true,
    },
})

const { t } = useI18n()
const emit = defineEmits(['activate', 'deactivate'])
</script>
<template>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ t('admin.users.table.name') }}</th>
                    <th>{{ t('admin.users.table.email') }}</th>
                    <th class="text-center">{{ t('admin.users.table.registry') }}</th>
                    <th class="text-center">{{ t('admin.users.table.confirmed') }}</th>
                    <th class="text-center">{{ t('admin.users.table.status') }}</th>
                    <th class="text-center">{{ t('admin.users.table.action') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>
                        <div class="d-flex align-items-center gap-2 no-background">
                            <UserAvatar :name="user.name" :color="user.color" />
                            <span class="d-none d-lg-inline">{{ user.name }}</span>
                        </div>
                    </td>

                    <td>
                        {{ user.email }}
                    </td>

                    <td class="text-center">
                        {{ user.createdAt }}
                    </td>

                    <td class="text-center">
                        <StatusBadge
                            :type="user.isVerified ? 'confirmed' : 'pending'"
                            :title="
                                user.isVerified
                                    ? t('admin.users.user.verification.confirmed')
                                    : t('admin.users.user.verification.pending')
                            "
                        />
                    </td>

                    <td class="text-center">
                        <StatusBadge
                            :type="user.isActive ? 'active' : 'inactive'"
                            :title="
                                user.isActive
                                    ? t('admin.users.user.activity.active')
                                    : t('admin.users.user.activity.inactive')
                            "
                        />
                    </td>

                    <td class="text-center">
                        <button
                            :class="[
                                'btn me-2',
                                user.isActive ? 'text-danger bg-danger-subtle' : 'text-success bg-success-subtle',
                            ]"
                            @click="user.isActive ? emit('deactivate', user.id) : emit('activate', user.id)"
                        >
                            <span class="d-none d-lg-inline">
                                {{
                                    user.isActive ? t('admin.users.action.deactivate') : t('admin.users.action.active')
                                }}
                            </span>
                            <i :class="['bi d-inline d-lg-none', user.isActive ? 'bi bi-x-lg' : 'bi-check-lg']"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

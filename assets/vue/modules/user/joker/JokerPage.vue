<script setup>
import api from '@/services/api.js'
import { onMounted, ref } from 'vue'
import EditableVersion from '@/modules/user/joker/jokerPage/EditableVersion.vue'
import ViewVersion from '@/modules/user/joker/jokerPage/ViewVersion.vue'
import AppLoader from '@/components/loaders/AppLoader.vue'
import { useI18n } from 'vue-i18n'
import toast from '@/services/toast.js'

const { t } = useI18n()
const joker = ref(null)
const jokerEditable = ref(null)
const loading = ref(true)

const loadJoker = async () => {
    try {
        loading.value = true
        const { data } = await api.get('/api/user/joker')
        joker.value = data.teamId
        jokerEditable.value = data.editable
    } catch (e) {
        toast.error(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
    }
}

onMounted(loadJoker)
</script>
<template>
    <AppLoader v-if="loading" />
    <EditableVersion v-if="!loading && jokerEditable" :joker-id="joker" />
    <ViewVersion v-if="!loading && !jokerEditable" :joker-id="joker" />
</template>

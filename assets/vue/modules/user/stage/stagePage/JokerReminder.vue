<script setup>
import JokerIcon from '../../../../../icons/card-joker-svgrepo-com.svg'
import api from '@/services/api.js'
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const jokerToSelect = ref(false)

const loadJoker = async () => {
    try {
        const response = await api.get('/api/user/joker')
        jokerToSelect.value = response.data.editable && !response.data.teamId
    } catch (e) {
        alert(e.response?.data?.message ?? t('common.errors.500'))
    }
}

onMounted(loadJoker)
</script>
<template>
    <div v-if="jokerToSelect" class="card mt-3 p-3 joker-reminder bg-warning-subtle text-center">
        <router-link class="nav-link" :to="{ name: 'joker' }">
            <h2 class="text-danger"><JokerIcon class="joker-icon me-2" />{{ t('user.stage.jokerReminder.title') }}</h2>
            <span class="text-secondary">{{ t('user.stage.jokerReminder.description') }}</span>
        </router-link>
    </div>
</template>
<style lang="scss">
.joker-reminder {
    .joker-icon {
        height: 60px;
    }
}
</style>

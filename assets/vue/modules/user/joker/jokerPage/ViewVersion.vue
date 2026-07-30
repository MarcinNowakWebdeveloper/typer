<script setup>
import api from '@/services/api.js'
import { onMounted, ref } from 'vue'
import UserAvatar from '@/components/users/UserAvatar.vue'
import JokerIcon from '../../../../../icons/card-joker-svgrepo-com.svg'
import AppLoader from '@/components/loaders/AppLoader.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const loading = ref(true)
const jokers = ref(null)

const loadJokers = async () => {
    try {
        loading.value = true
        const { data } = await api.get('/api/jokers')
        jokers.value = data
    } catch (e) {
        alert(e.response?.data?.message ?? t('common.errors.500'))
    } finally {
        loading.value = false
    }
}

onMounted(loadJokers)
</script>
<template>
    <AppLoader v-if="loading" />
    <div v-else class="joker">
        <div class="card p-3 mb-3">
            <div class="d-flex align-items-center">
                <JokerIcon class="joker-icon me-2 color-primary" />
                <h2>{{ t('user.joker.viewPage.title') }}</h2>
            </div>
        </div>
        <div class="jokers-list card p-3">
            <div v-for="joker in jokers" :key="joker.id" class="row align-items-start mb-2">
                <div class="col-3 col d-flex">
                    <img :src="`/file/${joker.logo}`" :alt="joker.team" class="team-logo me-2" loading="lazy" />
                    <span class="d-none d-lg-inline">{{ joker.team }}</span>
                </div>
                <div class="col-9 d-flex flex-wrap gap-2">
                    <span v-for="user in joker.users" :key="user">
                        <UserAvatar :name="user" />
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.joker {
    .joker-icon {
        height: 48px;
    }
}
</style>

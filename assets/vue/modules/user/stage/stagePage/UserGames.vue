<script setup>
import { computed } from 'vue'
import EditablePage from '@/modules/user/stage/stagePage/userGames/EditablePage.vue'
import ViewPage from '@/modules/user/stage/stagePage/userGames/ViewPage.vue'
import ListPage from '@/modules/user/stage/stagePage/userGames/ListPage.vue'

const props = defineProps({
    game: {
        type: Object,
        required: true,
    },
    userHomeGoals: {
        type: Number,
        default: null,
    },
    userAwayGoals: {
        type: Number,
        default: null,
    },
})

const start_date = props.game.date + ' ' + props.game.time

const editable = computed(() => {
    return new Date(start_date).getTime() > Date.now()
})
</script>
<template>
    <div :class="['border border-1 mt-2 rounded', editable ? 'game-edit' : 'game-view ']">
        <EditablePage
            v-if="editable"
            :game="props.game"
            :home-goals="props.userHomeGoals"
            :away-goals="props.userAwayGoals"
        />
        <ViewPage v-else :game="props.game" :home-goals="props.userHomeGoals" :away-goals="props.userAwayGoals" />
        <ListPage :game-id="props.game.id" />
    </div>
</template>

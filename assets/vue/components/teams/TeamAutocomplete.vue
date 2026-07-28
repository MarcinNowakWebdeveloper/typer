<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const isOpen = ref(false)
const search = ref('')
const { t } = useI18n()
const props = defineProps({
    modelValue: {
        type: Number,
        default: null,
    },
    teams: {
        type: Array,
        required: true,
    },
    excludedTeamId: {
        type: Number,
        default: null,
    },
})
const emit = defineEmits(['update:modelValue'])
const selectedTeam = computed(() => props.teams.find((t) => t.id === props.modelValue))
const normalizedSearch = computed(() => search.value.trim().toLowerCase())
const filteredTeams = computed(() =>
    props.teams.filter((team) => {
        if (team.id === props.excludedTeamId) {
            return false
        }

        return team.name.toLowerCase().includes(normalizedSearch.value)
    }),
)

const selectTeam = (team) => {
    emit('update:modelValue', team.id)

    search.value = ''
    isOpen.value = false
}
</script>
<template>
    <div class="autocomplete">
        <input
            v-if="!selectedTeam || isOpen"
            v-model="search"
            class="form-control"
            :placeholder="t('admin.config.game.form.placeholder.searchTeam')"
            @focus="isOpen = true"
            @blur="isOpen = false"
        />

        <button
            v-else
            type="button"
            class="form-control d-flex align-items-center gap-2 text-start"
            @click="isOpen = true"
        >
            <img v-if="selectedTeam.logo" :src="`/api/file/${selectedTeam.logo.id}`" class="logo" />

            {{ selectedTeam.name }}
        </button>

        <div v-if="isOpen && filteredTeams.length" class="autocomplete-options">
            <button
                v-for="team in filteredTeams"
                :key="team.id"
                type="button"
                class="autocomplete-option"
                @mousedown.prevent="selectTeam(team)"
            >
                <img v-if="team.logo" :src="`/api/file/${team.logo.id}`" :alt="team.name" />

                <span>{{ team.name }}</span>
            </button>
        </div>
    </div>
</template>
<style lang="scss" scoped>
.autocomplete {
    position: relative;
    display: flex;
    gap: 0.5rem;

    img.logo {
        width: 24px;
        height: 24px;
    }
}

.autocomplete-add {
    flex: 0 0 auto;
}

.autocomplete-options {
    position: absolute;
    top: calc(100% + 0.25rem);
    right: 3rem;
    left: 0;
    z-index: 20;
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid rgba(var(--primary-rgb), 0.35);
    border-radius: 0.375rem;
    background: #fff;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
}

.autocomplete-option {
    display: flex;
    align-items: center;
    width: 100%;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 0;
    background: transparent;
    text-align: left;

    &:hover,
    &:focus {
        background-color: rgba(var(--primary-rgb), 0.12);
    }

    img {
        width: 28px;
        height: 28px;
        object-fit: contain;
    }
}
</style>

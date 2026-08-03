import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useSettingsStore = defineStore('settings', () => {
    const pointCountingStrategies = ref(localStorage.getItem('pointCountingStrategies') ?? null)

    watch(pointCountingStrategies, (value) => {
        localStorage.setItem('pointCountingStrategies', value)
    })

    return {
        pointCountingStrategies,
    }
})

import { ref, onMounted } from 'vue'

const now = ref(Date.now())

let interval = null

export function useCountdown() {
    onMounted(() => {
        if (!interval) {
            interval = setInterval(() => {
                now.value = Date.now()
            }, 1000)
        }
    })

    const getCountdown = (dateString) => {
        const diff = new Date(dateString).getTime() - now.value

        if (diff <= 0) {
            return {
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0,
                finished: true,
            }
        }

        return {
            days: Math.floor(diff / 86400000),
            hours: Math.floor((diff / 3600000) % 24),
            minutes: Math.floor((diff / 60000) % 60),
            seconds: Math.floor((diff / 1000) % 60),
            finished: false,
        }
    }

    return {
        getCountdown,
    }
}

import { defineStore } from 'pinia'

let id = 1

export const useToastStore = defineStore('toast', {
    state: () => ({
        toasts: [],
    }),

    actions: {
        show(message, type = 'primary') {
            const toast = {
                id: id++,
                message,
                type,
            }

            this.toasts.push(toast)

            setTimeout(() => {
                this.remove(toast.id)
            }, 5000)
        },

        remove(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id)
        },
    },
})

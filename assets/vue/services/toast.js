import { useToastStore } from '@/stores/toast'

export default {
    success(message) {
        useToastStore().show(message, 'success')
    },

    error(message) {
        useToastStore().show(message, 'danger')
    },

    warning(message) {
        useToastStore().show(message, 'warning')
    },

    info(message) {
        useToastStore().show(message, 'info')
    },
}

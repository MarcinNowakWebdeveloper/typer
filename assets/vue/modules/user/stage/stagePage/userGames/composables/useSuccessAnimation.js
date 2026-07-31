import { ref, onBeforeUnmount } from 'vue'

export function useSuccessAnimation(timeout = 3000) {
    const saved = ref(false)

    let timer = null

    const showSuccess = () => {
        saved.value = false

        requestAnimationFrame(() => {
            saved.value = true

            clearTimeout(timer)

            timer = setTimeout(() => {
                saved.value = false
            }, timeout)
        })
    }

    onBeforeUnmount(() => {
        clearTimeout(timer)
    })

    return {
        saved,
        showSuccess,
    }
}

import { onUnmounted, ref } from 'vue'

export function useImagePreview(logo) {
    const image = ref(null)
    const preview = ref(logo ? `/api/file/${logo}` : '')
    const onFileChange = (event) => {
        const file = event.target.files?.[0]

        if (!file) return

        if (preview.value?.startsWith('blob:')) {
            URL.revokeObjectURL(preview.value)
        }

        image.value = file
        preview.value = URL.createObjectURL(file)
    }

    onUnmounted(() => {
        if (preview.value?.startsWith('blob:')) {
            URL.revokeObjectURL(preview.value)
        }
    })

    return {
        image,
        preview,
        onFileChange,
    }
}

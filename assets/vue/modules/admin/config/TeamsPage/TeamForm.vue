<script setup>
import { useI18n } from 'vue-i18n'
import { useImagePreview } from '@/modules/admin/config/TeamsPage/composables/useImagePreview.js'
import { useTeamForm } from '@/modules/admin/config/TeamsPage/composables/useTeamForm.js'

const { t } = useI18n()

const props = defineProps({
    id: {
        type: Number,
        default: null,
    },
    name: {
        type: String,
        default: '',
    },
    logo: {
        type: Number,
        default: null,
    },
})
const emit = defineEmits(['saved', 'close'])

const { image, preview, onFileChange } = useImagePreview(props.logo)

const { form, loading, submit, close } = useTeamForm(props, emit, image)
</script>

<template>
    <div class="card p-3 team-edit">
        <div class="row mb-4">
            <h4 class="col">
                {{ !props.id ? t('admin.config.teamPage.form.title.new') : t('admin.config.teamPage.form.title.edit') }}
            </h4>

            <button v-if="props.id" class="btn btn-light col-auto me-3" @click="close()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form @submit.prevent="submit">
            <div class="row mb-3">
                <div class="col-12 col-lg-auto mb-3">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        :placeholder="t('admin.config.teamPage.form.placeholder.name')"
                        required
                    />
                </div>
                <div class="col-12 col-lg-auto d-flex align-items-center gap-2 mb-3">
                    <img v-if="preview" :src="preview" class="team-logo" />
                    <label class="btn btn-outline-primary">
                        <i class="bi bi-image"></i>
                        {{ t('admin.config.teamPage.form.file') }}
                        <input type="file" accept="image/*" class="d-none" @change="onFileChange" />
                    </label>
                </div>
                <div class="col-12 col-lg-auto">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <i :class="['bi', !props.id ? 'bi-plus-lg' : 'bi-floppy']"></i>
                        {{ loading ? t('common.saving') : t('common.action.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
<style lang="scss" scoped>
.team-edit.card {
    border-color: rgba(var(--primary-rgb), 0.5);
    background-color: rgba(var(--primary-rgb), 0.15);

    input {
        border-color: rgba(var(--primary-rgb), 0.5);
        background-color: transparent;
    }
}
</style>

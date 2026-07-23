import { createI18n } from 'vue-i18n'

import commonPl from './locales/pl.js'
import adminUsersPl from '@/modules/admin/users/locales/pl.js'
import adminConfigPl from '@/modules/admin/config/locales/pl.js'
import componentsNavigationPl from '@/components/navigation/locales/pl.js'
import authPl from '@/modules/auth/locales/pl.js'

const i18n = createI18n({
    legacy: false,
    locale: 'pl',
    fallbackLocale: 'pl',
    messages: {
        pl: {
            admin: {
                users: adminUsersPl,
                config: adminConfigPl,
            },
            auth: authPl,
            common: commonPl,
            components: {
                navigation: componentsNavigationPl,
            },
        },
    },
})

export default i18n

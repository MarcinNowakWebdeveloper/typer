import { createI18n } from 'vue-i18n'

import commonPl from './locales/pl.js'
import adminUsersPl from '@/modules/admin/users/locales/pl.js'
import adminConfigPl from '@/modules/admin/config/locales/pl.js'
import componentsNavigationPl from '@/components/navigation/locales/pl.js'
import authPl from '@/modules/auth/locales/pl.js'
import userJokerPl from '@/modules/user/joker/locales/pl.js'
import userStagePL from '@/modules/user/stage/locales/pl.js'
import dashboardPl from '@/modules/dashboard/locales/pl.js'
import chartsPl from '@/modules/charts/locales/pl.js'
import profilePl from '@/modules/user/profile/locales/pl.js'

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
            charts: chartsPl,
            common: commonPl,
            components: {
                navigation: componentsNavigationPl,
            },
            user: {
                joker: userJokerPl,
                stage: userStagePL,
                profile: profilePl,
            },
            dashboard: dashboardPl,
        },
    },
})

export default i18n

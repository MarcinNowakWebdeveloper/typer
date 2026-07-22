import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import '@/styles/app.css'
import i18n from './modules/i18n'
import tooltip from '@/directive/tooltip.js'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(i18n)
app.directive('tooltip', tooltip)

app.mount('#app')

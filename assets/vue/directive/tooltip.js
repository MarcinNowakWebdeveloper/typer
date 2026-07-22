import { Tooltip } from 'bootstrap'

export default {
    mounted(el) {
        new Tooltip(el, {
            trigger: 'click',
        })
    },
}

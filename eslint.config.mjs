import js from '@eslint/js'
import pluginVue from 'eslint-plugin-vue'
import globals from 'globals'
import prettier from 'eslint-config-prettier'

export default [
    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],

    {
        files: ['**/*.{js,mjs,cjs,vue}'],
        languageOptions: {
            globals: globals.browser,
        },
    },
    prettier,
]

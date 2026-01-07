// @ts-nocheck

import pluginVue from 'eslint-plugin-vue'
import pluginPrettier from 'eslint-plugin-prettier'
import prettierConfig from 'eslint-config-prettier'
import vueParser from 'vue-eslint-parser'

export default [
  {
    files: ['**/*.{js,ts,vue}'],

    languageOptions: {
      parser: vueParser,
      parserOptions: {
        parser: '@typescript-eslint/parser',
        ecmaVersion: 'latest',
        sourceType: 'module',
      },
    },

    plugins: {
      vue: pluginVue,
      prettier: pluginPrettier,
    },

    rules: {
      // Vue rules
      'vue/multi-word-component-names': 'off',

      // ✅ Prettier rules
      'prettier/prettier': [
        'error',
        {
          semi: false,
          singleQuote: true,
          trailingComma: 'es5',
          endOfLine: 'auto',
        },
      ],
    },

    linterOptions: {
      reportUnusedDisableDirectives: true,
    },

    ignores: ['dist/**', 'node_modules/**'],
  },
  prettierConfig,
]

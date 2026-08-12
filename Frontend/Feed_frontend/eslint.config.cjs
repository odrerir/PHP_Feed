const vuePlugin = require('eslint-plugin-vue')
const vueParser = require('vue-eslint-parser')

module.exports = [
  {
    ignores: ['node_modules/**', 'dist/**', 'public/**', 'vite.config.js', 'package-lock.json', '/.output/**'],
  },
  {
    files: ['**/*.js', '**/*.vue'],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        ecmaVersion: 2021,
        sourceType: 'module',
      },
    },
    plugins: {
      vue: vuePlugin,
    },
    rules: {
      'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
      'vue/multi-word-component-names': 'off',
    },
  },
]

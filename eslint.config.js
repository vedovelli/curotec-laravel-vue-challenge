import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

import prettier from 'eslint-config-prettier';
import vue from 'eslint-plugin-vue';

export default defineConfigWithVueTs(
    vue.configs['flat/essential'],
    vue.configs['flat/strongly-recommended'],
    vue.configs['flat/recommended'],
    vueTsConfigs.recommended,
    {
        ignores: ['vendor', 'node_modules', 'public', 'bootstrap/ssr', 'tailwind.config.js', 'resources/js/components/ui/*', 'dist'],
    },
    {
        rules: {
            'vue/multi-word-component-names': 'off',
            'vue/no-v-html': 'off',
            '@typescript-eslint/no-explicit-any': 'warn',
            '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
            '@typescript-eslint/explicit-function-return-type': 'off',
            '@typescript-eslint/explicit-module-boundary-types': 'off',
        },
    },
    prettier,
);

import { createI18n } from 'vue-i18n';
import bn from '../locales/bn.json';
import en from '../locales/en.json';

export function setupI18n(locale = 'bn') {
    return createI18n({
        legacy: false,
        locale,
        fallbackLocale: 'bn',
        messages: { bn, en },
    });
}

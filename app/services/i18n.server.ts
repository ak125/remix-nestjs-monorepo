import { createCookie } from "@remix-run/node";
import { cache } from "~/lib/cache.server";

const SUPPORTED_LANGUAGES = ['fr', 'en'] as const;
type Language = typeof SUPPORTED_LANGUAGES[number];

const languageCookie = createCookie("preferred-language", {
  maxAge: 31_536_000, // 1 year
});

export class I18nService {
  private translations: Record<Language, Record<string, string>> = {
    fr: {},
    en: {}
  };

  async getLanguage(request: Request) {
    const cookieHeader = request.headers.get("Cookie");
    const language = await languageCookie.parse(cookieHeader);
    
    if (language && SUPPORTED_LANGUAGES.includes(language)) {
      return language;
    }

    const acceptLanguage = request.headers.get("Accept-Language");
    const preferredLanguage = acceptLanguage?.split(',')[0].split('-')[0];
    
    return SUPPORTED_LANGUAGES.includes(preferredLanguage as Language) 
      ? preferredLanguage 
      : 'fr';
  }

  async getTranslations(language: Language) {
    const cacheKey = `translations:${language}`;
    const cached = await cache.get(cacheKey);
    if (cached) return cached;

    const translations = await import(`../i18n/${language}.json`);
    await cache.set(cacheKey, translations, 60 * 60); // 1 hour
    
    return translations;
  }

  async setLanguage(language: Language) {
    if (!SUPPORTED_LANGUAGES.includes(language)) {
      throw new Error('Language not supported');
    }

    return languageCookie.serialize(language);
  }
}

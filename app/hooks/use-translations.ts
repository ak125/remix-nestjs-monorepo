import { useMatches } from "@remix-run/react";

export function useTranslations() {
  const matches = useMatches();
  const { i18n } = matches[0].data;

  return {
    t: (key: string) => {
      const keys = key.split('.');
      let current = i18n;
      
      for (const k of keys) {
        if (current?.[k] === undefined) {
          console.warn(`Translation missing: ${key}`);
          return key;
        }
        current = current[k];
      }

      return current;
    },
    language: i18n.language
  };
}

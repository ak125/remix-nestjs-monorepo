import { useSiteConfig } from "~/hooks/use-site-config";

/**
 * Format a number as currency using the site's currency configuration
 */
export function formatCurrency(amount: number): string {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 2
  }).format(amount);
}

/**
 * Format a percentage with 2 decimal places
 */
export function formatPercent(value: number): string {
  return new Intl.NumberFormat('fr-FR', {
    style: 'percent',
    minimumFractionDigits: 2
  }).format(value / 100);
}

/**
 * Nettoie et normalise un texte en remplaçant les entités HTML, sauts de ligne, etc.
 */
export function contentCleaner(text: string): string {
  if (!text) return '';
  
  return text
    .replace(/\n|\r|\t/g, " ")
    .replace(/&agrave;/g, "à")
    .replace(/&eacute;/g, "é")
    .replace(/&egrave;/g, "è")
    .replace(/&ecirc;/g, "ê")
    .replace(/&ocirc;/g, "ô")
    .replace(/&icirc;/g, "î")
    .replace(/&rsquo;/g, "'")
    .replace(/&#39;/g, "'")
    .replace(/&nbsp;/g, " ")
    .replace(/http:\/\/w/g, "https://w")
    .replace(/\s+/g, " ")
    .replace(/\.\./g, ".");
}

/**
 * Nettoie une chaîne pour la recherche en supprimant les caractères spéciaux
 */
export function clearSearchQuery(text: string): string {
  if (!text) return '';
  return text.replace(/[\(\)\[\],''\s./_*-]/g, "");
}

/**
 * Création d'un slug SEO
 */
export function createSlug(text: string): string {
  if (!text) return '';
  
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '') // remove accents
    .replace(/[^\w\s-]/g, '') // remove special chars
    .replace(/\s+/g, '-') // replace spaces with hyphens
    .replace(/--+/g, '-') // replace multiple hyphens with single hyphen
    .trim()
    .replace(/^-+|-+$/g, ''); // trim hyphens from start and end
}

/**
 * Sélectionne un élément aléatoire dans un tableau
 */
export function getRandomElement<T>(array: T[]): T | null {
  if (!array.length) return null;
  return array[Math.floor(Math.random() * array.length)];
}

/**
 * Génère une phrase SEO avec un élément aléatoire
 */
export function generateSeoPhrase(
  markers: string[], 
  prefix: string = '', 
  suffix: string = ''
): string {
  const marker = getRandomElement(markers);
  if (!marker) return '';
  
  let phrase = marker;
  if (prefix) phrase = `${prefix} ${phrase}`;
  if (suffix) phrase = `${phrase} ${suffix}`;
  
  return phrase;
}

/**
 * Formate un nombre avec la devise
 */
export function formatPrice(
  price: number, 
  currency: string = '€', 
  decimals: number = 2
): string {
  return price.toFixed(decimals).replace('.', ',') + ' ' + currency;
}

/**
 * Calcule le TTC à partir d'un HT
 */
export function calculateWithVAT(price: number, vatRate: number = 20): number {
  return price * (1 + vatRate / 100);
}

import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export interface SiteWebsite {
  address: string;
  email: string;
  phone: string;
  phoneToCall: string;
}

export interface SiteOwner {
  name: string;
  domain: string;
}

export interface SiteConfig {
  domain: string;
  domainName: string;
  domainParent: string;
  domainWebsite: SiteWebsite;
  owner: SiteOwner;
  directories: {
    blog: string;
    blogTitle: string;
    client: string;
    entretien: string;
    entretienTitle: string;
    constructeurs: string;
    constructeursTitle: string;
    guide: string;
    guideTitle: string;
  };
  currency: {
    symbol: string;
    vat: number;
    vatFormatted: string;
    vatCoeff: number;
  };
}

export class ConfigService {
  async getSiteConfig(language = 1): Promise<SiteConfig> {
    const cacheKey = `site-config:${language}`;
    const cachedConfig = await cache.get<SiteConfig>(cacheKey);
    
    if (cachedConfig) {
      return cachedConfig;
    }
    
    const config = await prisma.config.findFirst({
      where: { language }
    });
    
    if (!config) {
      throw new Error(`Configuration not found for language: ${language}`);
    }

    const siteConfig: SiteConfig = {
      domain: config.domain,
      domainName: config.name,
      domainParent: config.domain,
      domainWebsite: {
        address: config.address,
        email: config.email,
        phone: config.phone,
        phoneToCall: config.phoneCall
      },
      owner: {
        name: config.ownerName,
        domain: config.ownerDomain
      },
      directories: {
        blog: "blog-pieces-auto",
        blogTitle: "Blog automobile",
        client: "account",
        entretien: "conseils",
        entretienTitle: "Montage et entretien",
        constructeurs: "constructeurs",
        constructeursTitle: "Constructeurs",
        guide: "guide",
        guideTitle: "Guide d'achat"
      },
      currency: {
        symbol: "€",
        vat: 20,
        vatFormatted: (20).toFixed(2),
        vatCoeff: 1.2
      }
    };

    await cache.set(cacheKey, siteConfig, 60 * 60); // Cache for 1 hour
    
    return siteConfig;
  }
}

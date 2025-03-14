import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class MarqueService {
  constructor(private readonly prisma: PrismaService) {}

  async getMarqueDetails(marqueId: number) {
    const marque = await this.prisma.autoMarque.findFirst({
      where: { id: marqueId, isDisplayed: true },
      include: {
        seoData: true,
        modeles: {
          where: { isDisplayed: true },
          include: {
            types: { where: { isDisplayed: true } },
          },
        },
      },
    });

    if (!marque) {
      throw new NotFoundException('Marque introuvable ou non affichée.');
    }

    const pagetitle = marque.seoData
      ? marque.seoData.title.replace('#VMarque#', marque.name)
      : `Pièces détachées auto ${marque.name} neuves & d'origine`;

    const pagedescription = marque.seoData
      ? marque.seoData.description.replace('#VMarque#', marque.name)
      : `Achetez pour votre ${marque.name} des pièces détachées de qualité à prix réduit.`;

    const pagekeywords = marque.seoData ? marque.seoData.keywords : marque.name;

    const pageRobots = marque.relfollow ? 'index, follow' : 'noindex, nofollow';
    const canonicalLink = `https://mon-site.com/auto/${marque.alias}-${marque.id}.html`;

    return {
      marque: {
        id: marque.id,
        name: marque.name,
        alias: marque.alias,
        logo: marque.logo,
        relfollow: marque.relfollow,
      },
      seo: {
        title: pagetitle,
        description: pagedescription,
        keywords: pagekeywords,
        robots: pageRobots,
        canonical: canonicalLink,
      },
      modeles: marque.modeles.map((modele) => ({
        id: modele.id,
        name: modele.name,
        alias: modele.alias,
        types: modele.types.map((type) => ({
          id: type.id,
          name: type.name,
          alias: type.alias,
          puissance: type.powerPS,
          carrosserie: type.body,
          carburant: type.fuel,
          annee: type.yearFrom ? `de ${type.yearFrom} à ${type.yearTo ?? 'présent'}` : 'N/A',
        })),
      })),
    };
  }
}

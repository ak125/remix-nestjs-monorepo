import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class AutoService {
  constructor(private readonly prisma: PrismaService) {}

  async getAutoDetails(marqueId: number, modeleId: number, typeId: number) {
    const auto = await this.prisma.autoType.findFirst({
      where: {
        id: typeId,
        modele: {
          id: modeleId,
          marque: {
            id: marqueId,
            isDisplayed: true,
          },
          isDisplayed: true,
        },
        isDisplayed: true,
      },
      include: {
        modele: {
          include: {
            marque: true,
          },
        },
      },
    });

    if (!auto) {
      throw new NotFoundException('Aucune donnée trouvée pour ces paramètres.');
    }

    const pagetitle = `Pièces ${auto.modele.marque.name} ${auto.modele.name} ${auto.name}`;
    const pagedescription = `Catalogue pièces détachées pour ${auto.modele.marque.name} ${auto.modele.name} ${auto.name} - ${auto.powerPS} ch - ${auto.body}`;
    const pagekeywords = `${auto.modele.marque.name}, ${auto.modele.name}, ${auto.name}, ${auto.powerPS} ch, ${auto.yearFrom ?? ''}-${auto.yearTo ?? ''}`;

    return {
      marque: {
        name: auto.modele.marque.name,
        alias: auto.modele.marque.alias,
      },
      modele: {
        name: auto.modele.name,
        alias: auto.modele.alias,
      },
      type: {
        name: auto.name,
        alias: auto.alias,
        puissance: auto.powerPS,
        carrosserie: auto.body,
        carburant: auto.fuel,
        annee: auto.yearFrom ? `de ${auto.yearFrom} à ${auto.yearTo ?? 'présent'}` : 'N/A',
      },
      seo: {
        title: pagetitle,
        description: pagedescription, 
        keywords: pagekeywords,
      },
      robots: auto.modele.marque.isDisplayed && auto.modele.isDisplayed && auto.isDisplayed ? 'index, follow' : 'noindex, nofollow',
      canonical: `https://mon-site.com/auto/${auto.modele.marque.alias}-${marqueId}/${auto.modele.alias}-${modeleId}/${auto.alias}-${typeId}.html`,
    };
  }
}

import { Injectable, Logger, NotFoundException, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Inject } from '@nestjs/common';
import { Cache } from 'cache-manager';
import { Prisma } from '@prisma/client';

interface FilterOptions {
  gammeId?: string;
  marqueId?: string;
  year?: number;
  motorisation?: string;
  fuelType?: string;
  minPower?: number;
  maxPower?: number;
  page?: number;
  limit?: number;
}

@Injectable()
export class ModeleService {
  private readonly logger = new Logger(ModeleService.name);

  constructor(
    private readonly prisma: PrismaService,
    @Inject(CACHE_MANAGER) private readonly cacheManager: Cache
  ) {}

  // Récupère les années min et max pour une gamme et marque données
  async getMinMaxYear(gammeId: string, marqueId: string) {
    const cacheKey = `minmaxYear:${gammeId}:${marqueId}`;

    // Essayer de récupérer depuis le cache
    const cachedData = await this.cacheManager.get<{ minYear: number; maxYear: number }>(cacheKey);

    if (cachedData) {
      this.logger.debug(`Cache hit for ${cacheKey}`);
      return cachedData;
    }

    this.logger.debug(`Cache miss for ${cacheKey}, fetching from database`);
    return this.fetchAndCacheMinMaxYear(cacheKey, gammeId, marqueId);
  }

  // Récupère les données de la base et les met en cache
  private async fetchAndCacheMinMaxYear(cacheKey: string, gammeId: string, marqueId: string) {
    const currentYear = new Date().getFullYear();
    
    try {
      // Requête agrégée pour trouver les années min et max
      const results = await this.prisma.$queryRaw`
        SELECT 
          MIN(MODELE_YEAR_FROM) AS minYear, 
          MAX(COALESCE(MODELE_YEAR_TO, ${currentYear})) AS maxYear
        FROM AUTO_MODELE
        JOIN AUTO_TYPE ON TYPE_MODELE_ID = MODELE_ID  
        JOIN PIECES_RELATION_TYPE ON RTP_TYPE_ID = TYPE_ID
        JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID AND PIECE_PM_ID = RTP_PM_ID
        WHERE MODELE_MARQUE_ID = ${Number(marqueId)} 
        AND RTP_PG_ID = ${Number(gammeId)}
        AND MODELE_DISPLAY = 1 
        AND TYPE_DISPLAY = 1 
        AND PIECE_DISPLAY = 1
      `;

      // Traitement des résultats
      const result = Array.isArray(results) && results.length > 0 ? results[0] : null;
      
      const minYear = result?.minYear ? Number(result.minYear) : currentYear;
      const maxYear = result?.maxYear ? Number(result.maxYear) : currentYear;

      const data = { minYear, maxYear };
      
      // Mise en cache avec TTL de 10 minutes (600 secondes)
      await this.cacheManager.set(cacheKey, data, 600 * 1000);
      
      return data;
    } catch (error) {
      this.logger.error(`Error fetching min/max years: ${error.message}`, error.stack);
      return { minYear: currentYear - 5, maxYear: currentYear }; // Valeurs par défaut en cas d'erreur
    }
  }

  // Génère la liste complète des années entre min et max
  async getYearsList(gammeId: string, marqueId: string) {
    const { minYear, maxYear } = await this.getMinMaxYear(gammeId, marqueId);
    const years = [];
    
    // Générer la liste des années dans l'ordre décroissant
    for (let year = maxYear; year >= minYear; year--) {
      years.push({
        value: year,
        label: year.toString(),
        favorite: [2020, 2010, 2000].includes(year)
      });
    }

    return years;
  }

  // Récupère les modèles disponibles pour une marque, année et gamme données
  async getModelesForSelection(gammeId: string, marqueId: string, year: string) {
    const cacheKey = `modeles:${gammeId}:${marqueId}:${year}`;
    
    // Essayer de récupérer depuis le cache
    const cachedData = await this.cacheManager.get(cacheKey);
    
    if (cachedData) {
      this.logger.debug(`Cache hit for ${cacheKey}`);
      return cachedData;
    }

    this.logger.debug(`Cache miss for ${cacheKey}, fetching from database`);
    
    try {
      const yearNumber = Number(year);
      const currentYear = new Date().getFullYear();
      
      const modeles = await this.prisma.$queryRaw`
        SELECT DISTINCT 
          MODELE_ID, 
          MODELE_NAME,
          MODELE_YEAR_FROM,
          COALESCE(MODELE_YEAR_TO, ${currentYear}) AS MODELE_TO,
          MODELE_SORT
        FROM AUTO_MODELE
        JOIN AUTO_TYPE ON TYPE_MODELE_ID = MODELE_ID
        JOIN PIECES_RELATION_TYPE ON RTP_TYPE_ID = TYPE_ID
        JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID AND PIECE_PM_ID = RTP_PM_ID
        WHERE MODELE_MARQUE_ID = ${Number(marqueId)} 
        AND RTP_PG_ID = ${Number(gammeId)}
        AND MODELE_DISPLAY = 1 
        AND TYPE_DISPLAY = 1 
        AND PIECE_DISPLAY = 1
        AND MODELE_YEAR_FROM <= ${yearNumber}
        AND COALESCE(MODELE_YEAR_TO, ${currentYear}) >= ${yearNumber}
        ORDER BY MODELE_SORT
      `;
      
      // Transformer les résultats pour l'API
      const formattedModeles = Array.isArray(modeles) ? modeles.map(modele => ({
        id: modele.MODELE_ID,
        name: modele.MODELE_NAME,
        yearFrom: modele.MODELE_YEAR_FROM,
        yearTo: modele.MODELE_TO === currentYear ? 'Présent' : modele.MODELE_TO,
        sort: modele.MODELE_SORT
      })) : [];
      
      // Mettre en cache pour 10 minutes
      await this.cacheManager.set(cacheKey, formattedModeles, 600 * 1000);
      
      return formattedModeles;
    } catch (error) {
      this.logger.error(`Error fetching modeles: ${error.message}`, error.stack);
      return [];
    }
  }

  /**
   * Récupérer les modèles de voiture filtés par gamme, marque et année
   */
  async getModelesByParams(gammeId: string, marqueId: string, year: number) {
    if (!gammeId || !marqueId || !year) {
      throw new BadRequestException('Paramètres manquants');
    }
    
    const cacheKey = this.cacheService.createKey('modeles', { gammeId, marqueId, year });
    
    return this.prisma.getCached(cacheKey, async () => {
      const currentYear = new Date().getFullYear();
      
      try {
        const modeles = await this.prisma.modele.findMany({
          where: {
            marqueId: marqueId,
            display: true,
            yearFrom: { lte: year },
            OR: [
              { yearTo: { gte: year } },
              { yearTo: null }
            ],
            // S'assurer que le modèle a des types associés qui sont visibles
            types: {
              some: { 
                display: true
              }
            }
          },
          include: {
            marque: true,
            types: {
              where: { 
                display: true
              },
              take: 5  // Limiter le nombre de types pour éviter des réponses trop volumineuses
            }
          },
          orderBy: [
            { sort: 'asc' },
            { name: 'asc' }
          ]
        });
        
        return modeles.map(modele => ({
          id: modele.id,
          name: modele.name,
          alias: modele.alias,
          yearFrom: modele.yearFrom,
          yearTo: modele.yearTo || currentYear,
          marque: {
            id: modele.marque.id,
            name: modele.marque.name
          },
          typesCount: modele.types.length
        }));
      } catch (error) {
        console.error('Error fetching modeles:', error);
        throw new NotFoundException('Impossible de récupérer les modèles');
      }
    }, 600); // Cache pour 10 minutes
  }
  
  /**
   * Récupération avancée des modèles avec filtres dynamiques
   */
  async getFilteredModeles(filters: FilterOptions) {
    const { 
      gammeId, 
      marqueId, 
      year, 
      motorisation, 
      fuelType,
      minPower,
      maxPower,
      page = 1,
      limit = 20
    } = filters;
    
    // Création d'une clé de cache basée sur tous les filtres
    const cacheKey = this.cacheService.createKey('modeles-filtered', filters);
    
    return this.prisma.getCached(cacheKey, async () => {
      // Construction des conditions de filtrage dynamiques
      const where: Prisma.ModeleWhereInput = {
        display: true,
      };
      
      if (marqueId) {
        where.marqueId = marqueId;
      }
      
      if (gammeId) {
        // Supposons qu'il y a une relation entre modèle et gamme
        where.gammes = {
          some: {
            gammeId
          }
        };
      }
      
      if (year) {
        where.yearFrom = { lte: year };
        where.OR = [
          { yearTo: { gte: year } },
          { yearTo: null }
        ];
      }
      
      // Filtrage sur les types de véhicules
      const typeFilters: Prisma.TypeWhereInput = {
        display: true
      };
      
      if (motorisation) {
        typeFilters.name = { contains: motorisation };
      }
      
      if (fuelType) {
        typeFilters.fuel = { equals: fuelType };
      }
      
      if (minPower !== undefined) {
        typeFilters.powerPS = { gte: minPower };
      }
      
      if (maxPower !== undefined) {
        typeFilters.powerPS = { 
          ...typeFilters.powerPS,
          lte: maxPower 
        };
      }
      
      // Si des filtres de type sont définis, les ajouter à la requête
      if (motorisation || fuelType || minPower !== undefined || maxPower !== undefined) {
        where.types = {
          some: typeFilters
        };
      }
      
      // Calcul de la pagination
      const skip = (page - 1) * limit;
      
      try {
        // Exécution de la requête avec pagination
        const [modeles, total] = await Promise.all([
          this.prisma.modele.findMany({
            where,
            include: {
              marque: {
                select: {
                  id: true,
                  name: true,
                  alias: true,
                  logo: true
                }
              },
              types: {
                where: typeFilters,
                take: 5
              }
            },
            orderBy: [
              { sort: 'asc' },
              { name: 'asc' }
            ],
            skip,
            take: limit
          }),
          
          // Compter le nombre total de résultats pour la pagination
          this.prisma.modele.count({ where })
        ]);
        
        // Formater la réponse
        return {
          data: modeles.map(modele => ({
            id: modele.id,
            name: modele.name,
            alias: modele.alias,
            yearFrom: modele.yearFrom,
            yearTo: modele.yearTo,
            marque: modele.marque,
            types: modele.types,
            typesCount: modele.types.length
          })),
          pagination: {
            total,
            page,
            limit,
            totalPages: Math.ceil(total / limit)
          }
        };
      } catch (error) {
        console.error('Error in getFilteredModeles:', error);
        throw new NotFoundException('Impossible de récupérer les modèles');
      }
    }, 600); // Cache pour 10 minutes
  }

  /**
   * Récupère tous les modèles avec pagination et recherche
   */
  async getAllModeles(options: {
    page?: number;
    limit?: number;
    search?: string;
    marqueId?: string;
    yearFrom?: number;
    yearTo?: number;
  }) {
    const {
      page = 1,
      limit = 20,
      search = '',
      marqueId,
      yearFrom,
      yearTo
    } = options;
    
    const skip = (page - 1) * limit;
    const take = limit;
    
    // Construction du filtre de recherche dynamique
    const where: Prisma.ModeleWhereInput = {
      display: true,
    };
    
    // Recherche par nom
    if (search && search.trim() !== '') {
      where.OR = [
        { name: { contains: search, mode: 'insensitive' } },
        { alias: { contains: search, mode: 'insensitive' } }
      ];
    }
    
    // Filtre par marque
    if (marqueId) {
      where.marqueId = marqueId;
    }
    
    // Filtres par année
    if (yearFrom) {
      where.yearFrom = { gte: yearFrom };
    }
    
    if (yearTo) {
      where.yearTo = { 
        OR: [
          { lte: yearTo },
          { equals: null }
        ] 
      };
    }
    
    try {
      // Exécution des requêtes en parallèle pour optimiser les performances
      const [modeles, total] = await Promise.all([
        this.prisma.modele.findMany({
          where,
          include: {
            marque: {
              select: {
                id: true,
                name: true,
                nameMeta: true,
                alias: true
              }
            }
          },
          orderBy: [
            { sort: 'asc' },
            { name: 'asc' }
          ],
          skip,
          take
        }),
        this.prisma.modele.count({ where })
      ]);
      
      // Formatage de la réponse avec métadonnées de pagination
      return {
        data: modeles,
        pagination: {
          total,
          page,
          limit,
          totalPages: Math.ceil(total / limit)
        }
      };
    } catch (error) {
      this.logger.error(`Error getting modeles: ${error.message}`, error.stack);
      throw new BadRequestException('Impossible de récupérer les modèles');
    }
  }

  // CRUD Operations
  
  /**
   * Créer un nouveau modèle
   */
  async createModele(data: {
    name: string;
    alias: string;
    marqueId: string;
    yearFrom: number;
    yearTo?: number | null;
    sort?: number;
  }) {
    try {
      const newModele = await this.prisma.modele.create({
        data: {
          ...data,
          display: true,
          sort: data.sort || 0
        }
      });
      
      // Invalider le cache après création
      await this.prisma.invalidateCachePrefix('modeles');
      
      return newModele;
    } catch (error) {
      console.error('Error creating model:', error);
      if (error instanceof Prisma.PrismaClientKnownRequestError) {
        if (error.code === 'P2002') {
          throw new BadRequestException('Un modèle avec cet alias existe déjà');
        }
      }
      throw new BadRequestException('Impossible de créer le modèle');
    }
  }
  
  /**
   * Mettre à jour un modèle existant
   */
  async updateModele(id: string, data: {
    name?: string;
    alias?: string;
    marqueId?: string;
    yearFrom?: number;
    yearTo?: number | null;
    display?: boolean;
    sort?: number;
  }) {
    try {
      const updatedModele = await this.prisma.modele.update({
        where: { id },
        data
      });
      
      // Invalider le cache après mise à jour
      await this.prisma.invalidateCachePrefix('modeles');
      
      return updatedModele;
    } catch (error) {
      if (error instanceof Prisma.PrismaClientKnownRequestError) {
        if (error.code === 'P2025') {
          throw new NotFoundException(`Modèle avec l'ID ${id} non trouvé`);
        }
        if (error.code === 'P2002') {
          throw new BadRequestException('Un modèle avec cet alias existe déjà');
        }
      }
      throw new BadRequestException('Impossible de mettre à jour le modèle');
    }
  }
  
  /**
   * Supprimer un modèle
   */
  async deleteModele(id: string) {
    try {
      // Supprimer d'abord les types qui dépendent de ce modèle
      await this.prisma.type.deleteMany({
        where: { modeleId: id }
      });
      
      // Ensuite supprimer le modèle
      const deletedModele = await this.prisma.modele.delete({
        where: { id }
      });
      
      // Invalider le cache après suppression
      await this.prisma.invalidateCachePrefix('modeles');
      
      return deletedModele;
    } catch (error) {
      if (error instanceof Prisma.PrismaClientKnownRequestError) {
        if (error.code === 'P2025') {
          throw new NotFoundException(`Modèle avec l'ID ${id} non trouvé`);
        }
      }
      throw new BadRequestException('Impossible de supprimer le modèle');
    }
  }
}

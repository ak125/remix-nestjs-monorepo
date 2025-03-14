import { Injectable, Logger, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Inject } from '@nestjs/common';
import { Cache } from 'cache-manager';
import { Prisma } from '@prisma/client';
import { CreateModelDto, UpdateModelDto } from './dto/model.dto';

interface ModelQueryParams {
  carMarqueId?: string;
  gammeId?: string;
  search?: string;
  page?: number;
  limit?: number;
  sort?: string;
  order?: 'asc' | 'desc';
}

@Injectable()
export class ModelsService {
  private readonly logger = new Logger(ModelsService.name);

  constructor(
    private prisma: PrismaService,
    @Inject(CACHE_MANAGER) private cacheManager: Cache
  ) {}

  /**
   * Récupère les modèles avec pagination et filtres
   */
  async getModels(options: {
    carMarqueId?: string | number;
    gammeId?: string | number;
    search?: string;
    page?: number;
    pageSize?: number;
  }) {
    const { 
      carMarqueId,
      gammeId,
      search = '',
      page = 1,
      pageSize = 10
    } = options;
    
    // Créer une clé de cache unique basée sur les paramètres
    const cacheKey = `models:${carMarqueId}:${gammeId}:${search}:${page}:${pageSize}`;
    
    // Vérifier si les données sont en cache
    const cachedData = await this.cacheManager.get(cacheKey);
    if (cachedData) {
      this.logger.debug(`Cache hit for key: ${cacheKey}`);
      return cachedData;
    }
    
    this.logger.debug(`Cache miss for key: ${cacheKey}`);
    
    // Construire la requête avec les filtres
    const where: any = {};
    
    if (carMarqueId) {
      where.marqueId = Number(carMarqueId);
    }
    
    if (gammeId) {
      where.gammeId = Number(gammeId);
    }
    
    if (search) {
      where.OR = [
        { name: { contains: search, mode: 'insensitive' } },
        { alias: { contains: search, mode: 'insensitive' } }
      ];
    }
    
    // Exécuter la requête pour récupérer les données et le compte total
    const [models, total] = await Promise.all([
      this.prisma.modele.findMany({
        where,
        select: { 
          id: true, 
          name: true, 
          yearFrom: true, 
          yearTo: true,
          marqueId: true,
          marque: {
            select: {
              name: true
            }
          }
        },
        orderBy: [
          { yearFrom: 'desc' },
          { name: 'asc' }
        ],
        skip: (page - 1) * pageSize,
        take: pageSize,
      }),
      this.prisma.modele.count({ where })
    ]);
    
    // Calculer les métadonnées de pagination
    const totalPages = Math.ceil(total / pageSize);
    
    // Préparer la réponse
    const result = {
      models,
      pagination: {
        total,
        page,
        pageSize,
        totalPages
      }
    };
    
    // Mettre en cache pour 60 secondes
    await this.cacheManager.set(cacheKey, result, 60 * 1000);
    
    return result;
  }

  /**
   * Récupère un modèle par son ID
   */
  async getModelById(id: number) {
    const cacheKey = `model:${id}`;
    
    // Vérifier si le modèle est en cache
    const cachedModel = await this.cacheManager.get(cacheKey);
    if (cachedModel) {
      return cachedModel;
    }
    
    const model = await this.prisma.modele.findUnique({
      where: { id },
      include: {
        marque: {
          select: {
            id: true,
            name: true
          }
        }
      }
    });
    
    if (model) {
      // Mettre en cache pour 5 minutes
      await this.cacheManager.set(cacheKey, model, 5 * 60 * 1000);
    }
    
    return model;
  }

  /**
   * Crée un nouveau modèle
   */
  async createModel(data: any) {
    const model = await this.prisma.modele.create({
      data
    });
    
    // Invalider les caches potentiellement affectés
    await this.invalidateCaches();
    
    return model;
  }

  /**
   * Met à jour un modèle existant
   */
  async updateModel(id: number, data: any) {
    const model = await this.prisma.modele.update({
      where: { id },
      data
    });
    
    // Invalider les caches potentiellement affectés
    await this.invalidateCaches();
    await this.cacheManager.del(`model:${id}`);
    
    return model;
  }

  /**
   * Supprime un modèle
   */
  async deleteModel(id: number) {
    try {
      const model = await this.prisma.modele.delete({
        where: { id }
      });
      
      // Invalider les caches potentiellement affectés
      await this.invalidateCaches();
      await this.cacheManager.del(`model:${id}`);
      
      return { success: true, model };
    } catch (error) {
      this.logger.error(`Error deleting model ${id}:`, error);
      
      if (error.code === 'P2003') {
        return { 
          success: false, 
          error: 'Ce modèle est référencé par d\'autres éléments et ne peut pas être supprimé' 
        };
      }
      
      return { 
        success: false, 
        error: 'Une erreur est survenue lors de la suppression du modèle' 
      };
    }
  }

  /**
   * Récupère la plage d'années disponibles pour une combinaison gamme+marque
   */
  async getModelYears(gammeId: string, marqueId: string): Promise<Array<{value: number, label: string, favorite: boolean}>> {
    this.logger.debug(`Getting years range for gammeId=${gammeId}, marqueId=${marqueId}`);
    
    // Clé de cache unique pour cette requête
    const cacheKey = `model-years:${gammeId}:${marqueId}`;
    
    // Vérifie si la donnée est en cache
    const cachedData = await this.cacheManager.get(cacheKey);
    if (cachedData) {
      this.logger.debug('Returning years from cache');
      return cachedData as Array<{value: number, label: string, favorite: boolean}>;
    }
    
    // Année courante
    const thisYear = new Date().getFullYear();
    
    try {
      // Requête pour obtenir les années min et max
      const result = await this.prisma.$queryRaw`
        SELECT 
          MIN(yearFrom) AS minYear, 
          MAX(COALESCE(yearTo, ${thisYear})) AS maxYear
        FROM Modele
        JOIN ModeleType ON ModeleType.modeleId = Modele.id
        JOIN PieceRelationType ON PieceRelationType.typeId = ModeleType.id
        JOIN Piece ON Piece.id = PieceRelationType.pieceId AND Piece.pgId = PieceRelationType.pgId
        WHERE Modele.marqueId = ${parseInt(marqueId, 10)}
          AND PieceRelationType.pgId = ${parseInt(gammeId, 10)}
          AND Modele.display = true 
          AND ModeleType.display = true 
          AND Piece.display = true
      `;
      
      // Si pas de résultat, renvoie un tableau vide
      if (!result || !Array.isArray(result) || result.length === 0 || 
          !result[0].minYear || !result[0].maxYear) {
        return [];
      }
      
      // Années min et max trouvées
      const minYear = parseInt(result[0].minYear as string, 10);
      const maxYear = parseInt(result[0].maxYear as string, 10);
      
      // Générer le tableau d'années
      const years = [];
      for (let year = maxYear; year >= minYear; year--) {
        // Marquer certaines années comme favorites (comme dans le PHP)
        const favorite = [2020, 2010, 2000].includes(year);
        years.push({
          value: year,
          label: year.toString(),
          favorite
        });
      }
      
      // Mettre en cache pour 1 heure (ces données changent rarement)
      await this.cacheManager.set(cacheKey, years, 60 * 60 * 1000);
      
      return years;
    } catch (error) {
      this.logger.error(`Error fetching model years: ${error.message}`, error.stack);
      return [];
    }
  }

  /**
   * Invalide les caches liés aux modèles
   */
  private async invalidateCaches() {
    // Dans une implémentation réelle, vous pourriez utiliser une fonctionnalité
    // plus avancée comme les clés par pattern de Redis
    this.logger.debug('Invalidating model caches');
    const keysToDelete = await this.cacheManager.store.keys('models:*');
    
    const deletePromises = keysToDelete.map(key => 
      this.cacheManager.del(key)
    );
    
    await Promise.all(deletePromises);
  }

  /**
   * Invalide les caches liés aux années de modèles
   */
  private async invalidateYearsCaches() {
    this.logger.debug('Invalidating model years caches');
    const keysToDelete = await this.cacheManager.store.keys('model-years:*');
    
    const deletePromises = keysToDelete.map(key => 
      this.cacheManager.del(key)
    );
    
    await Promise.all(deletePromises);
  }
}

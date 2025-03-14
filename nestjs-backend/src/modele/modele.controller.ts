import { 
  Controller, 
  Get, 
  Post,
  Put,
  Delete,
  Patch,
  Query, 
  Param, 
  Body, 
  ValidationPipe, 
  ParseIntPipe,
  DefaultValuePipe,
  Logger,
  UseGuards
} from '@nestjs/common';
import { ModeleService } from './modele.service';
import { CreateModeleDto, UpdateModeleDto } from './dto/modele.dto';
import { AdminAuthGuard } from '../auth/guards/admin-auth.guard';

// DTO pour la validation des entrées
class CreateModeleDto {
  name: string;
  alias: string;
  marqueId: string;
  yearFrom: number;
  yearTo?: number;
  sort?: number;
}

class UpdateModeleDto {
  name?: string;
  alias?: string;
  marqueId?: string;
  yearFrom?: number;
  yearTo?: number;
  display?: boolean;
  sort?: number;
}

@Controller('api/modele')
export class ModeleController {
  private readonly logger = new Logger(ModeleController.name);
  
  constructor(private readonly modeleService: ModeleService) {}

  // Récupérer tous les modèles avec pagination et recherche
  @Get('all')
  @UseGuards(AdminAuthGuard)
  async getAllModeles(
    @Query('page', new DefaultValuePipe(1), ParseIntPipe) page: number = 1,
    @Query('limit', new DefaultValuePipe(20), ParseIntPipe) limit: number = 20,
    @Query('search') search?: string,
    @Query('marqueId') marqueId?: string,
    @Query('yearFrom', new DefaultValuePipe(0), ParseIntPipe) yearFrom: number = 0,
    @Query('yearTo', new DefaultValuePipe(0), ParseIntPipe) yearTo: number = 0
  ) {
    this.logger.debug(`Getting modeles with page=${page}, limit=${limit}, search=${search}, marqueId=${marqueId}`);
    
    return this.modeleService.getAllModeles({
      page,
      limit,
      search,
      marqueId,
      yearFrom: yearFrom > 0 ? yearFrom : undefined,
      yearTo: yearTo > 0 ? yearTo : undefined
    });
  }

  // Route pour les anciens endpoints (compatibilité avec le code legacy)
  @Get('search')
  async getModelesByParams(
    @Query('gammeId') gammeId: string,
    @Query('marqueId') marqueId: string,
    @Query('year') year: string
  ) {
    if (!gammeId || !marqueId || !year) {
      return [];
    }
    
    try {
      const yearNum = Number(year);
      if (isNaN(yearNum)) {
        return [];
      }
      
      return await this.modeleService.getModelesByParams(gammeId, marqueId, yearNum);
    } catch (error) {
      console.error('Controller error:', error);
      return [];
    }
  }
  
  // Nouvelle route avec filtres avancés
  @Get()
  async getFilteredModeles(
    @Query('gammeId') gammeId?: string,
    @Query('marqueId') marqueId?: string,
    @Query('year') year?: string,
    @Query('motorisation') motorisation?: string,
    @Query('fuelType') fuelType?: string,
    @Query('minPower', new DefaultValuePipe(0), ParseIntPipe) minPower?: number,
    @Query('maxPower') maxPower?: string,
    @Query('page', new DefaultValuePipe(1), ParseIntPipe) page?: number,
    @Query('limit', new DefaultValuePipe(20), ParseIntPipe) limit?: number
  ) {
    const filterOptions = {
      gammeId,
      marqueId,
      year: year ? Number(year) : undefined,
      motorisation,
      fuelType,
      minPower,
      maxPower: maxPower ? Number(maxPower) : undefined,
      page,
      limit
    };
    
    return this.modeleService.getFilteredModeles(filterOptions);
  }
  
  // CRUD Operations
  
  // Créer un nouveau modèle
  @Post()
  @UseGuards(AdminAuthGuard)
  async createModele(@Body(ValidationPipe) createDto: CreateModeleDto) {
    this.logger.log(`Creating new modele: ${JSON.stringify(createDto)}`);
    return this.modeleService.createModele(createDto);
  }
  
  // Mettre à jour un modèle
  @Patch(':id')
  @UseGuards(AdminAuthGuard)
  async updateModele(
    @Param('id') id: string,
    @Body(ValidationPipe) updateDto: UpdateModeleDto
  ) {
    this.logger.log(`Updating modele ${id}: ${JSON.stringify(updateDto)}`);
    return this.modeleService.updateModele(id, updateDto);
  }
  
  // Suppression d'un modèle
  @Delete(':id')
  @UseGuards(AdminAuthGuard)
  async deleteModele(@Param('id') id: string) {
    this.logger.log(`Deleting modele ${id}`);
    return this.modeleService.deleteModele(id);
  }
  
  // Route pour obtenir tous les modèles d'une marque
  @Get('marque/:marqueId')
  async getModelesByMarque(@Param('marqueId') marqueId: string) {
    if (!marqueId) {
      return [];
    }
    
    try {
      return await this.modeleService.getModelesByMarque(marqueId);
    } catch (error) {
      return [];
    }
  }
  
  // Route pour obtenir un modèle spécifique
  @Get(':id')
  async getModeleById(@Param('id') id: string) {
    if (!id) {
      return { error: 'ID manquant' };
    }
    
    try {
      return await this.modeleService.getModeleById(id);
    } catch (error) {
      return { error: error.message };
    }
  }

  @Get('annees')
  async getAnnees(
    @Query('gammeId') gammeId: string,
    @Query('marqueId') marqueId: string,
  ) {
    this.logger.debug(`Getting years list for gammeId=${gammeId}, marqueId=${marqueId}`);
    
    if (!gammeId || !marqueId) {
      return { error: 'Les paramètres gammeId et marqueId sont requis.' };
    }
    
    try {
      return await this.modeleService.getYearsList(gammeId, marqueId);
    } catch (error) {
      this.logger.error(`Error fetching years: ${error.message}`, error.stack);
      return { error: 'Une erreur est survenue lors de la récupération des années.' };
    }
  }

  @Get('selection')
  async getModelesForSelection(
    @Query('gammeId') gammeId: string,
    @Query('marqueId') marqueId: string,
    @Query('year') year: string,
  ) {
    this.logger.debug(`Getting modeles for gammeId=${gammeId}, marqueId=${marqueId}, year=${year}`);
    
    if (!gammeId || !marqueId || !year) {
      return { error: 'Les paramètres gammeId, marqueId et year sont requis.' };
    }
    
    try {
      return await this.modeleService.getModelesForSelection(gammeId, marqueId, year);
    } catch (error) {
      this.logger.error(`Error fetching modeles: ${error.message}`, error.stack);
      return { error: 'Une erreur est survenue lors de la récupération des modèles.' };
    }
  }
  
  @Get('compatibility')
  async checkCompatibility(
    @Query('gammeId') gammeId: string,
    @Query('marqueId') marqueId: string,
    @Query('year') year: string,
  ) {
    if (!gammeId || !marqueId || !year) {
      return { compatible: false, message: 'Paramètres incomplets' };
    }
    
    try {
      const modeles = await this.modeleService.getModelesForSelection(gammeId, marqueId, year);
      return {
        compatible: modeles.length > 0,
        count: modeles.length,
        message: modeles.length > 0 
          ? `${modeles.length} modèles compatibles trouvés` 
          : 'Aucun modèle compatible trouvé'
      };
    } catch (error) {
      this.logger.error(`Error checking compatibility: ${error.message}`);
      return { compatible: false, message: 'Erreur lors de la vérification' };
    }
  }
}

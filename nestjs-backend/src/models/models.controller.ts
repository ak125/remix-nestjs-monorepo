import { 
  Controller, 
  Get, 
  Post, 
  Put, 
  Delete, 
  Body, 
  Param, 
  Query,
  ParseIntPipe,
  DefaultValuePipe,
  UseGuards,
  Logger
} from '@nestjs/common';
import { ModelsService } from './models.service';
import { AuthGuard } from '../auth/guards/auth.guard';
import { AdminGuard } from '../auth/guards/admin.guard';

@Controller('api/models')
export class ModelsController {
  private readonly logger = new Logger(ModelsController.name);
  
  constructor(private readonly modelsService: ModelsService) {}

  @Get()
  async getModels(
    @Query('carMarqueId') carMarqueId?: string,
    @Query('gammeId') gammeId?: string,
    @Query('search') search?: string,
    @Query('page', new DefaultValuePipe(1), ParseIntPipe) page: number = 1,
    @Query('pageSize', new DefaultValuePipe(10), ParseIntPipe) pageSize: number = 10,
  ) {
    this.logger.log(`Fetching models with params: carMarqueId=${carMarqueId}, gammeId=${gammeId}, search=${search}, page=${page}, pageSize=${pageSize}`);
    
    return this.modelsService.getModels({
      carMarqueId,
      gammeId,
      search,
      page,
      pageSize
    });
  }

  @Get(':id')
  async getModel(@Param('id', ParseIntPipe) id: number) {
    return this.modelsService.getModelById(id);
  }

  @Post()
  @UseGuards(AuthGuard, AdminGuard)
  async createModel(@Body() modelData: any) {
    this.logger.log(`Creating new model: ${JSON.stringify(modelData)}`);
    return this.modelsService.createModel(modelData);
  }

  @Put(':id')
  @UseGuards(AuthGuard, AdminGuard)
  async updateModel(
    @Param('id', ParseIntPipe) id: number,
    @Body() modelData: any
  ) {
    this.logger.log(`Updating model ${id}: ${JSON.stringify(modelData)}`);
    return this.modelsService.updateModel(id, modelData);
  }

  @Delete(':id')
  @UseGuards(AuthGuard, AdminGuard)
  async deleteModel(@Param('id', ParseIntPipe) id: number) {
    this.logger.log(`Deleting model ${id}`);
    return this.modelsService.deleteModel(id);
  }

  @Get('years')
  async getModelYears(
    @Query('gammeId') gammeId: string,
    @Query('marqueId') marqueId: string
  ) {
    this.logger.log(`Fetching model years with params: gammeId=${gammeId}, marqueId=${marqueId}`);
    
    if (!gammeId || !marqueId) {
      return { error: 'Les paramètres gammeId et marqueId sont requis' };
    }
    
    try {
      const years = await this.modelsService.getModelYears(gammeId, marqueId);
      return years;
    } catch (error) {
      this.logger.error(`Error getting model years: ${error.message}`, error.stack);
      return { error: 'Une erreur est survenue lors de la récupération des années disponibles' };
    }
  }
}

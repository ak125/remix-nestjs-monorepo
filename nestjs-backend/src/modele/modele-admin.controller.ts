import { 
  Controller, 
  Get, 
  Post, 
  Put, 
  Delete, 
  Body, 
  Param, 
  UseGuards, 
  ValidationPipe 
} from '@nestjs/common';
import { ModeleService } from './modele.service';
import { AdminGuard } from '../auth/guards/admin.guard';

// DTO pour la validation des entrées (partagé avec le contrôleur principal)
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

@Controller('api/admin/modele')
@UseGuards(AdminGuard) // Protection des routes pour les administrateurs uniquement
export class ModeleAdminController {
  constructor(private readonly modeleService: ModeleService) {}

  @Post()
  async createModele(@Body(ValidationPipe) createDto: CreateModeleDto) {
    return this.modeleService.createModele(createDto);
  }

  @Put(':id')
  async updateModele(
    @Param('id') id: string,
    @Body(ValidationPipe) updateDto: UpdateModeleDto
  ) {
    return this.modeleService.updateModele(id, updateDto);
  }

  @Delete(':id')
  async deleteModele(@Param('id') id: string) {
    return this.modeleService.deleteModele(id);
  }
  
  // Toggle the display status of a model
  @Put(':id/toggle-display')
  async toggleModelDisplay(@Param('id') id: string) {
    const modele = await this.prisma.modele.findUnique({ where: { id } });
    if (!modele) {
      throw new NotFoundException(`Modèle avec l'ID ${id} non trouvé`);
    }
    
    return this.modeleService.updateModele(id, { display: !modele.display });
  }
}

import {
  Controller,
  Get,
  Patch,
  Body,
  UseGuards,
  Req,
  BadRequestException,
} from '@nestjs/common';
import { UsersService } from './users.service';
import { LocalAuthGuard } from '../../auth/local-auth.guard';
import { Request } from 'express';
import { UpdateAddressDto } from './dto/update-address.dto';
import { ChangePasswordDto } from './dto/change-password.dto';

@Controller('users')
export class UsersController {
  constructor(private readonly usersService: UsersService) {}

  /**
   * ✅ Récupérer le profil utilisateur connecté
   */
  @UseGuards(LocalAuthGuard)
  @Get('me')
  async getUserProfile(@Req() req: Request) {
    const user = req.user as { id: string }; // Correction typage

    if (!user?.id) {
      throw new BadRequestException('Utilisateur non authentifié.');
    }

    return this.usersService.getUserById(user.id);
  }

  /**
   * ✅ Mettre à jour l'adresse utilisateur (avec validation Zod)
   */
  @UseGuards(LocalAuthGuard)
  @Patch('address')
  async updateAddress(@Req() req: Request, @Body() body: unknown) {
    const user = req.user as { id: string };

    if (!user?.id) {
      throw new BadRequestException('Utilisateur non authentifié.');
    }

    // ✅ Validation avec Zod
    const parsedData = UpdateAddressDto.safeParse(body);
    if (!parsedData.success) {
      throw new BadRequestException(parsedData.error.format());
    }

    return this.usersService.updateAddress(user.id, parsedData.data);
  }

  /**
   * ✅ Changer le mot de passe (avec validation Zod)
   */
  @UseGuards(LocalAuthGuard)
  @Patch('change-password')
  async changePassword(@Req() req: Request, @Body() body: unknown) {
    const user = req.user as { id: string };

    if (!user?.id) {
      throw new BadRequestException('Utilisateur non authentifié.');
    }

    // ✅ Validation avec Zod
    const parsedData = ChangePasswordDto.safeParse(body);
    if (!parsedData.success) {
      throw new BadRequestException(parsedData.error.format());
    }

    return this.usersService.changePassword(user.id, parsedData.data);
  }
}

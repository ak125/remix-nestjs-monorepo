import {
  Injectable,
  BadRequestException,
  NotFoundException,
} from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { ChangePasswordDto } from './dto/change-password.dto';
import { UpdateAddressDto } from './dto/update-address.dto';
import * as crypto from 'crypto';

@Injectable()
export class UsersService {
  constructor(private readonly prisma: PrismaService) {}

  /**
   * ✅ Récupérer un utilisateur par son ID
   */
  async getUserById(userId: string) {
    const user = await this.prisma.xTR_CUSTOMER.findUnique({
      where: { CST_ID: userId },
      select: {
        CST_ID: true,
        CST_MAIL: true,
        CST_CIVILITY: true,
        CST_NAME: true,
        CST_FNAME: true,
        CST_ADDRESS: true,
        CST_ZIP_CODE: true,
        CST_CITY: true,
        CST_COUNTRY: true,
        CST_TEL: true,
        CST_GSM: true,
        CST_IS_PRO: true,
        CST_RS: true,
        CST_SIRET: true,
      },
    });

    if (!user) throw new NotFoundException('Utilisateur non trouvé');
    return user;
  }

  /**
   * ✅ Mettre à jour l'adresse utilisateur
   */
  async updateAddress(userId: string, addressData: typeof UpdateAddressDto) {
    // Vérification avec Zod
    const parsedData = UpdateAddressDto.safeParse(addressData);
    if (!parsedData.success) {
      throw new BadRequestException(parsedData.error.format());
    }

    const user = await this.prisma.xTR_CUSTOMER.findUnique({
      where: { CST_ID: userId },
    });

    if (!user) throw new NotFoundException('Utilisateur introuvable');

    return this.prisma.xTR_CUSTOMER.update({
      where: { CST_ID: userId },
      data: {
        CST_CIVILITY: parsedData.data.civ,
        CST_NAME: parsedData.data.nom,
        CST_FNAME: parsedData.data.prenom,
        CST_TEL: parsedData.data.tel,
        CST_GSM: parsedData.data.gsm,
        CST_ADDRESS: parsedData.data.adr,
        CST_ZIP_CODE: parsedData.data.zipcode,
        CST_CITY: parsedData.data.ville,
        CST_COUNTRY: parsedData.data.pays,
      },
    });
  }

  /**
   * ✅ Changer le mot de passe utilisateur
   */
  async changePassword(userId: string, changePasswordDto: typeof ChangePasswordDto) {
    // Vérification avec Zod
    const parsedData = ChangePasswordDto.safeParse(changePasswordDto);
    if (!parsedData.success) {
      throw new BadRequestException(parsedData.error.format());
    }

    const user = await this.prisma.xTR_CUSTOMER.findUnique({
      where: { CST_ID: userId },
      select: { CST_PSWD: true },
    });

    if (!user) throw new BadRequestException('Utilisateur introuvable.');

    // Vérification de l'ancien mot de passe (crypté)
    const oldPasswordHash = this.hashPassword(parsedData.data.oldPassword);
    if (user.CST_PSWD !== oldPasswordHash) {
      throw new BadRequestException('L’ancien mot de passe est incorrect.');
    }

    // Cryptage du nouveau mot de passe
    const newPasswordHash = this.hashPassword(parsedData.data.newPassword);

    // Mise à jour du mot de passe
    return this.prisma.xTR_CUSTOMER.update({
      where: { CST_ID: userId },
      data: { CST_PSWD: newPasswordHash },
    });
  }

  /**
   * 🔑 Fonction de hachage de mot de passe avec `crypto`
   */
  private hashPassword(password: string): string {
    return crypto.createHash('md5').update(password).digest('hex');
  }
}

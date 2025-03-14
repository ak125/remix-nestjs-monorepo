import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { ConfigService } from '@nestjs/config';
import * as crypto from 'crypto';

@Injectable()
export class AuthService {
  constructor(
    private prisma: PrismaService,
    private config: ConfigService
  ) {}

  async verifySession(session: any) {
    const log = session.im7mylog;
    const mykey = session.im7mykey;

    if (!mykey || mykey === crypto.createHash('md5').update('default').digest('hex')) {
      return this.getAccessResponse('expired');
    }

    const reseller = await this.prisma.resellerAccess.findFirst({
      where: { login: log, keylog: mykey },
    });

    if (!reseller) {
      return this.getAccessResponse('refused');
    }

    return reseller.valide === 1 
      ? this.getAccessResponse('granted', reseller.id)
      : this.getAccessResponse('suspended');
  }

  async validateAdmin(params: { 
    login: string;
    keylog: string;
  }) {
    const { login, keylog } = params;

    const admin = await this.prisma.cONFIG_ADMIN.findFirst({
      where: {
        CNFA_LOGIN: login,
        CNFA_KEYLOG: keylog,
        CNFA_LEVEL: {
          gt: 6
        }
      }
    });

    if (!admin || !admin.CNFA_ACTIV) {
      throw new UnauthorizedException('Accès refusé ou compte suspendu');
    }

    return admin;
  }

  private getAccessResponse(status: 'expired' | 'refused' | 'suspended' | 'granted', ssid = 0) {
    const responses = {
      expired: {
        destinationLink: '/access-expired',
        destinationLinkMsg: 'Expired',
      },
      refused: {
        destinationLink: '/access-refused', 
        destinationLinkMsg: 'Denied',
      },
      suspended: {
        destinationLink: '/access-suspended',
        destinationLinkMsg: 'Suspended',
      },
      granted: {
        destinationLink: '/access-permitted',
        destinationLinkMsg: 'Granted',
      }
    };

    return {
      ...responses[status],
      ssid,
      accessRequest: status === 'granted',
      destinationLinkGranted: status === 'granted' ? 1 : 0
    };
  }
}

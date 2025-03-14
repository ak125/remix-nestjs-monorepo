import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { createClient } from '@supabase/supabase-js';
import { ConfigService } from '@nestjs/config';
import * as crypto from 'crypto';

@Injectable()
export class AuthService {
  private supabase;

  constructor(
    private prisma: PrismaService,
    private config: ConfigService
  ) {
    this.supabase = createClient(
      config.get('SUPABASE_URL')!,
      config.get('SUPABASE_ANON_KEY')!
    );
  }

  async validateAdmin(login: string, keylog: string) {
    const admin = await this.prisma.admin.findFirst({
      where: {
        login,
        keylog,
        active: true,
        level: { gte: 6 }
      }
    });

    if (!admin) {
      throw new UnauthorizedException('Invalid credentials');
    }

    return admin;
  }

  async createSession(adminId: string) {
    const sessionToken = crypto.randomBytes(32).toString('hex');
    
    await this.supabase
      .from('admin_sessions')
      .insert({
        admin_id: adminId,
        token: sessionToken,
        expires_at: new Date(Date.now() + 24 * 60 * 60 * 1000)
      });

    return sessionToken;
  }

  async verifySession(token: string) {
    const { data: session } = await this.supabase
      .from('admin_sessions')
      .select('*')
      .eq('token', token)
      .gt('expires_at', new Date().toISOString())
      .single();

    return session;
  }
}

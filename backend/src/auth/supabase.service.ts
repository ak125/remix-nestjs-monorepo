import { Injectable, UnauthorizedException } from '@nestjs/common';
import { createClient, SupabaseClient } from '@supabase/supabase-js';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class SupabaseService {
  private supabase: SupabaseClient;

  constructor(private prisma: PrismaService) {
    this.supabase = createClient(
      process.env.SUPABASE_URL,
      process.env.SUPABASE_ANON_KEY
    );
  }

  async signUp(email: string, password: string) {
    const { data: { user }, error } = await this.supabase.auth.signUp({
      email,
      password
    });

    if (error) throw new UnauthorizedException(error.message);

    // Create user profile in our database
    await this.prisma.user.create({
      data: {
        id: user.id,
        email: user.email,
        preferences: {
          create: {} // Create default preferences
        }
      }
    });

    return user;
  }

  async signIn(email: string, password: string) {
    const { data: { session }, error } = await this.supabase.auth.signInWithPassword({
      email,
      password
    });

    if (error) throw new UnauthorizedException(error.message);
    return session;
  }

  async verifyToken(token: string) {
    const { data: { user }, error } = await this.supabase.auth.getUser(token);
    if (error) throw new UnauthorizedException(error.message);
    return user;
  }
}

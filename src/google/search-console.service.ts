import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { google } from 'googleapis';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class SearchConsoleService {
  private readonly logger = new Logger(SearchConsoleService.name);
  private readonly oauth2Client;

  constructor(
    private config: ConfigService,
    private prisma: PrismaService
  ) {
    this.oauth2Client = new google.auth.OAuth2(
      this.config.get('GOOGLE_CLIENT_ID'),
      this.config.get('GOOGLE_CLIENT_SECRET'),
      this.config.get('GOOGLE_REDIRECT_URI')
    );
  }

  getAuthUrl() {
    return this.oauth2Client.generateAuthUrl({
      access_type: 'offline',
      scope: ['https://www.googleapis.com/auth/webmasters.readonly']
    });
  }

  async exchangeCode(code: string) {
    try {
      const { tokens } = await this.oauth2Client.getToken(code);
      await this.saveTokens(tokens);
      return tokens;
    } catch (error) {
      this.logger.error('Error exchanging code:', error);
      throw error;
    }
  }

  private async saveTokens(tokens: any) {
    await this.prisma.googleTokens.upsert({
      where: { type: 'SEARCH_CONSOLE' },
      update: { 
        accessToken: tokens.access_token,
        refreshToken: tokens.refresh_token,
        expiresAt: new Date(tokens.expiry_date)
      },
      create: {
        type: 'SEARCH_CONSOLE',
        accessToken: tokens.access_token,
        refreshToken: tokens.refresh_token,
        expiresAt: new Date(tokens.expiry_date)
      }
    });
  }

  async getSitemaps(siteUrl: string) {
    try {
      const tokens = await this.getStoredTokens();
      this.oauth2Client.setCredentials(tokens);

      const searchConsole = google.webmasters({ 
        version: 'v3', 
        auth: this.oauth2Client 
      });

      const response = await searchConsole.sitemaps.list({ siteUrl });
      await this.saveSitemapsData(response.data.sitemap || []);

      return response.data.sitemap;
    } catch (error) {
      this.logger.error('Error fetching sitemaps:', error);
      throw error;
    }
  }

  private async getStoredTokens() {
    const tokens = await this.prisma.googleTokens.findUnique({
      where: { type: 'SEARCH_CONSOLE' }
    });

    if (!tokens) {
      throw new Error('No tokens found - please authenticate first');
    }

    return {
      access_token: tokens.accessToken,
      refresh_token: tokens.refreshToken,
      expiry_date: tokens.expiresAt.getTime()
    };
  }

  private async saveSitemapsData(sitemaps: any[]) {
    await Promise.all(
      sitemaps.map(sitemap => 
        this.prisma.sitemap.upsert({
          where: { url: sitemap.path },
          update: {
            lastDownloaded: new Date(sitemap.lastDownloaded),
            status: sitemap.status
          },
          create: {
            url: sitemap.path,
            lastDownloaded: new Date(sitemap.lastDownloaded),
            status: sitemap.status
          }
        })
      )
    );
  }
}

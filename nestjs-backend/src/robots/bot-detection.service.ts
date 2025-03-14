import { Injectable, Logger } from '@nestjs/common';
import { Cron } from '@nestjs/schedule';
import axios from 'axios';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class BotDetectionService {
  private readonly logger = new Logger(BotDetectionService.name);
  private defaultBlockedBots: string[] = [
    'AhrefsBot',
    'SemrushBot',
    'MJ12bot',
    'DotBot',
    'YandexBot',
    'BLEXBot',
    'Rogerbot'
  ];

  constructor(private readonly prisma: PrismaService) {
    this.initializeBots();
  }

  private async initializeBots() {
    try {
      const count = await this.prisma.blockedBot.count();
      
      // Si aucun bot n'est enregistré, ajouter les bots par défaut
      if (count === 0) {
        this.logger.log('Initialisation des bots bloqués par défaut');
        
        for (const bot of this.defaultBlockedBots) {
          await this.prisma.blockedBot.create({
            data: {
              name: bot,
              reason: 'Bot agressif connu',
              createdAt: new Date(),
              active: true
            }
          });
        }
      }
    } catch (error) {
      this.logger.error(`Erreur lors de l'initialisation des bots bloqués: ${error.message}`, error.stack);
    }
  }

  @Cron('0 0 * * *') // Tous les jours à minuit
  async updateBlockedBots() {
    this.logger.log('Mise à jour de la liste des bots bloqués');
    
    try {
      // Vous pouvez remplacer cette URL par une API réelle de liste de bots
      const response = await axios.get('https://api.example.com/bot-database/aggressive-bots');
      
      if (response.data && Array.isArray(response.data.bots)) {
        this.logger.log(`${response.data.bots.length} bots récupérés depuis l'API externe`);
        
        for (const bot of response.data.bots) {
          // Vérifier si le bot existe déjà
          const existingBot = await this.prisma.blockedBot.findFirst({
            where: { name: bot.name }
          });
          
          if (!existingBot) {
            await this.prisma.blockedBot.create({
              data: {
                name: bot.name,
                reason: 'Importé automatiquement depuis API',
                createdAt: new Date(),
                active: true
              }
            });
          }
        }
      }
    } catch (error) {
      this.logger.error(`Erreur lors de la mise à jour des bots bloqués: ${error.message}`, error.stack);
    }
  }

  async recordSuspiciousBot(userAgent: string, ipAddress: string, path: string) {
    try {
      // Vérifier si le bot est déjà enregistré
      const existingBot = await this.prisma.blockedBot.findFirst({
        where: { name: userAgent }
      });
      
      if (!existingBot) {
        // Enregistrer les détections de bots suspects
        await this.prisma.botDetection.create({
          data: {
            userAgent,
            ipAddress,
            path,
            detectedAt: new Date(),
            suspicious: true
          }
        });
        
        // Compter combien de fois ce bot a été détecté
        const detectionCount = await this.prisma.botDetection.count({
          where: { userAgent }
        });
        
        // Si détecté plus de 5 fois, bloquer automatiquement
        if (detectionCount >= 5) {
          await this.prisma.blockedBot.create({
            data: {
              name: userAgent,
              reason: 'Détecté automatiquement comme suspect',
              createdAt: new Date(),
              active: true
            }
          });
          
          this.logger.warn(`Bot suspect bloqué automatiquement: ${userAgent}`);
        }
      }
    } catch (error) {
      this.logger.error(`Erreur lors de l'enregistrement d'un bot suspect: ${error.message}`, error.stack);
    }
  }

  async getBlockedBots() {
    try {
      const blockedBots = await this.prisma.blockedBot.findMany({
        where: { active: true },
        select: { name: true, reason: true, createdAt: true }
      });
      
      return blockedBots.map(bot => bot.name);
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des bots bloqués: ${error.message}`, error.stack);
      return this.defaultBlockedBots; // Fallback aux valeurs par défaut
    }
  }

  // Nouvelles méthodes pour la gestion des bots
  
  async getBotsWithDetails() {
    try {
      return await this.prisma.blockedBot.findMany({
        where: { active: true },
        orderBy: { createdAt: 'desc' }
      });
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des détails des bots: ${error.message}`, error.stack);
      return this.defaultBlockedBots.map(name => ({ 
        name, 
        reason: 'Bot par défaut', 
        active: true, 
        createdAt: new Date() 
      }));
    }
  }
  
  async getBotsStats() {
    try {
      const [totalBlocked, totalDetections, recentDetections] = await Promise.all([
        this.prisma.blockedBot.count({ where: { active: true } }),
        this.prisma.botDetection.count(),
        this.prisma.botDetection.count({
          where: {
            detectedAt: {
              gte: new Date(Date.now() - 24 * 60 * 60 * 1000) // Dernières 24h
            }
          }
        })
      ]);
      
      return {
        totalBlocked,
        totalDetections,
        recentDetections,
        lastUpdated: new Date()
      };
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des statistiques: ${error.message}`, error.stack);
      return { 
        totalBlocked: this.defaultBlockedBots.length,
        totalDetections: 0,
        recentDetections: 0,
        lastUpdated: new Date()
      };
    }
  }
  
  async addBlockedBot(name: string, reason: string = 'Ajouté manuellement') {
    try {
      const existingBot = await this.prisma.blockedBot.findFirst({
        where: { name }
      });
      
      if (existingBot) {
        // Si le bot existe déjà mais est inactif, le réactiver
        if (!existingBot.active) {
          await this.prisma.blockedBot.update({
            where: { id: existingBot.id },
            data: { active: true, reason }
          });
          this.logger.log(`Bot ${name} réactivé`);
        } else {
          this.logger.log(`Bot ${name} déjà bloqué`);
        }
        return existingBot;
      }
      
      // Sinon, créer un nouveau bot bloqué
      const newBot = await this.prisma.blockedBot.create({
        data: {
          name,
          reason,
          createdAt: new Date(),
          active: true
        }
      });
      
      this.logger.log(`Nouveau bot ${name} ajouté à la liste des bloqués`);
      return newBot;
    } catch (error) {
      this.logger.error(`Erreur lors de l'ajout du bot ${name}: ${error.message}`, error.stack);
      throw error;
    }
  }
  
  async removeBlockedBot(name: string) {
    try {
      const bot = await this.prisma.blockedBot.findFirst({
        where: { name, active: true }
      });
      
      if (!bot) {
        this.logger.warn(`Bot ${name} non trouvé ou déjà inactif`);
        return { success: false, message: 'Bot non trouvé' };
      }
      
      // Marquer comme inactif plutôt que de supprimer
      await this.prisma.blockedBot.update({
        where: { id: bot.id },
        data: { active: false }
      });
      
      this.logger.log(`Bot ${name} désactivé`);
      return { success: true };
    } catch (error) {
      this.logger.error(`Erreur lors de la désactivation du bot ${name}: ${error.message}`, error.stack);
      throw error;
    }
  }
}

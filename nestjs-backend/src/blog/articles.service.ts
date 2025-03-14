import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';

@Injectable()
export class ArticlesService {
  private readonly logger = new Logger(ArticlesService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly eventEmitter: EventEmitter2
  ) {}

  async createArticle(data: any) {
    const article = await this.prisma.articleBlog.create({
      data
    });
    
    // Émettre un événement pour mettre à jour le sitemap et notifier Google
    this.eventEmitter.emit('content.created', { 
      type: 'article', 
      slug: article.slug, 
      id: article.id 
    });
    
    return article;
  }

  async updateArticle(id: number, data: any) {
    const article = await this.prisma.articleBlog.update({
      where: { id },
      data
    });
    
    // Publier ou dépublier un article déclenche des événements différents
    if (data.published === false) {
      this.eventEmitter.emit('content.disabled', { 
        type: 'article', 
        slug: article.slug, 
        id: article.id 
      });
    } else {
      // Mise à jour normale qui déclenchera aussi la notification des moteurs de recherche
      this.eventEmitter.emit('content.updated', { 
        type: 'article', 
        slug: article.slug, 
        id: article.id 
      });
    }
    
    return article;
  }

  async deleteArticle(id: number) {
    // Récupérer l'article avant de le supprimer pour avoir son slug
    const article = await this.prisma.articleBlog.findUnique({
      where: { id },
      select: { slug: true }
    });

    if (!article) {
      throw new Error('Article introuvable');
    }

    // Supprimer l'article
    await this.prisma.articleBlog.delete({
      where: { id }
    });

    // Émettre un événement pour mettre à jour le sitemap et notifier Google
    this.eventEmitter.emit('content.deleted', { 
      type: 'article', 
      slug: article.slug, 
      id 
    });
    
    return { success: true, message: 'Article supprimé avec succès' };
  }
}

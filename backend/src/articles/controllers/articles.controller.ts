import { Controller, Get, Param, Query, ParseIntPipe, NotFoundException } from '@nestjs/common';
import { ArticlesService } from '../services/articles.service';

@Controller('articles')
export class ArticlesController {
  constructor(private readonly articlesService: ArticlesService) {}

  @Get(':pieceId')
  async getArticle(
    @Param('pieceId', ParseIntPipe) pieceId: number,
    @Query('typeId', ParseIntPipe) typeId: number,
    @Query('pgId', ParseIntPipe) pgId: number,
    @Query('pmId', ParseIntPipe) pmId: number
  ) {
    const article = await this.articlesService.findOne({
      pieceId,
      typeId,
      pgId,
      pmId
    });

    if (!article) {
      throw new NotFoundException('Article introuvable');
    }

    return article;
  }
}

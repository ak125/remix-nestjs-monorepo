import { Controller, Get, Param, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { BlogService } from '../services/blog.service';

@Controller('blog')
@UseInterceptors(CacheInterceptor)
export class BlogController {
  constructor(private readonly blogService: BlogService) {}

  @Get(':marqueAlias/:mdgAlias')
  async getModelInfo(
    @Param('marqueAlias') marqueAlias: string,
    @Param('mdgAlias') mdgAlias: string
  ) {
    return this.blogService.getModelInfo({
      marqueAlias,
      mdgAlias
    });
  }

  @Get('articles/recent')
  async getRecentArticles() {
    return this.blogService.getRecentArticles();
  }

  @Get('guides')
  async getGuides() {
    return this.blogService.getGuides();
  }
}

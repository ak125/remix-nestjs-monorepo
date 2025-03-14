import { Controller, Get } from "@nestjs/common";
import { BlogGuideService } from "./blog-guide.service";

@Controller("blog-guide")
export class BlogGuideController {
  constructor(private readonly blogGuideService: BlogGuideService) {}

  @Get("recent")
  async getRecentArticles() {
    return this.blogGuideService.getRecentArticles();
  }

  @Get("most-read")
  async getMostReadArticles() {
    return this.blogGuideService.getMostReadArticles();
  }
}

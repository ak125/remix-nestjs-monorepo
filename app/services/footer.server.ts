import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export interface FooterMenuItem {
  id: number;
  title: string;
  alias: string;
  level: number;
  relFollow: boolean;
}

export class FooterService {
  async getFooterMenus(): Promise<FooterMenuItem[]> {
    const cacheKey = 'footer-menus';
    const cachedMenus = await cache.get<FooterMenuItem[]>(cacheKey);
    
    if (cachedMenus) {
      return cachedMenus;
    }
    
    const menus = await prisma.footerMenu.findMany({
      where: {
        active: true
      },
      orderBy: [
        { level: 'asc' },
        { position: 'asc' }
      ],
      select: {
        id: true,
        title: true,
        alias: true,
        level: true,
        relFollow: true
      }
    });
    
    await cache.set(cacheKey, menus, 60 * 60); // Cache for 1 hour
    
    return menus;
  }
  
  async getMenusByLevel(level: number): Promise<FooterMenuItem[]> {
    const menus = await this.getFooterMenus();
    return menus.filter(menu => menu.level === level);
  }
}

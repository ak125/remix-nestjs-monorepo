import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export interface FooterMenuGroup {
  level: number;
  title: string;
  items: FooterMenuItem[];
}

export interface FooterMenuItem {
  id: number;
  title: string;
  alias: string;
  level: number;
  relFollow: boolean;
}

export class FooterMenuService {
  async getAllMenus(): Promise<FooterMenuItem[]> {
    const cacheKey = 'footer-menus-all';
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
    });
    
    await cache.set(cacheKey, menus, 3600); // Cache for 1 hour
    return menus;
  }
  
  async getMenusByLevel(level: number): Promise<FooterMenuItem[]> {
    const cacheKey = `footer-menus-level-${level}`;
    const cachedMenus = await cache.get<FooterMenuItem[]>(cacheKey);
    
    if (cachedMenus) {
      return cachedMenus;
    }
    
    const menus = await prisma.footerMenu.findMany({
      where: { 
        level,
        active: true 
      },
      orderBy: { position: 'asc' },
    });
    
    await cache.set(cacheKey, menus, 3600); // Cache for 1 hour
    return menus;
  }
  
  async getMenuGroups(): Promise<FooterMenuGroup[]> {
    const allMenus = await this.getAllMenus();
    
    // Get unique levels with titles
    const levelGroups = Array.from(new Set(allMenus.map(menu => menu.level)))
      .sort((a, b) => a - b);
      
    const groups: FooterMenuGroup[] = [];
    
    for (const level of levelGroups) {
      const items = allMenus.filter(menu => menu.level === level);
      if (items.length > 0) {
        groups.push({
          level,
          title: this.getLevelTitle(level),
          items
        });
      }
    }
    
    return groups;
  }
  
  private getLevelTitle(level: number): string {
    switch (level) {
      case 1: return "Politiques";
      case 2: return "Support";
      case 3: return "Produits";
      case 4: return "Services";
      default: return `Menu ${level}`;
    }
  }
}

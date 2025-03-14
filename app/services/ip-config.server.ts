import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export type IPConfig = Record<string, string>;

export class IPConfigService {
  async getIPConfigs(): Promise<IPConfig> {
    const cacheKey = 'ip-configs';
    const cachedIPs = await cache.get<IPConfig>(cacheKey);
    
    if (cachedIPs) {
      return cachedIPs;
    }
    
    const ipList = await prisma.configIP.findMany();
    
    const ipConfigs = ipList.reduce((acc, ip) => {
      acc[ip.alias] = ip.value;
      return acc;
    }, {} as IPConfig);
    
    await cache.set(cacheKey, ipConfigs, 60 * 60); // Cache for 1 hour
    
    return ipConfigs;
  }
  
  async getIP(alias: string): Promise<string | null> {
    const ipConfigs = await this.getIPConfigs();
    return ipConfigs[alias] || null;
  }
}

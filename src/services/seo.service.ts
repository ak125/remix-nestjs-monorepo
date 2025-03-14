import { Injectable } from '@nestjs/common';

@Injectable()
export class SeoService {
  private readonly transliterationMap: Record<string, string> = {
    'ä': 'ae', 'æ': 'ae', 'ǽ': 'ae',
    'ö': 'oe', 'œ': 'oe',
    'ü': 'ue',
    'Ä': 'Ae', 'Ü': 'Ue', 'Ö': 'Oe',
    // ...more special characters
  };

  cleanUrlTitle(text: string): string {
    return text
      .toLowerCase()
      .replace(/[^\w\s-]/g, (char) => this.transliterationMap[char] || '')
      .replace(/[\s_-]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  getPathSegments(url: string): string[] {
    const paths = new URL(url).pathname.split('/');
    return paths.filter(Boolean);
  }

  getDepartmentAlias(url: string): string {
    const segments = this.getPathSegments(url);
    return segments[1] || '';
  }

  generateMetaTags(data: {
    title: string;
    description: string;
    robots?: string;
  }) {
    return {
      title: data.title,
      description: data.description,
      robots: data.robots || 'index, follow',
      ogTitle: data.title,
      ogDescription: data.description,
      ogSiteName: 'Automecanik',
      twitterCard: 'summary_large_image'
    };
  }
}

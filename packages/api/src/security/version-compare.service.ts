import { Injectable } from '@nestjs/common';
import { diffLines, Change } from 'diff';
import { z } from 'zod';

const compareSchema = z.object({
  oldContent: z.string(),
  newContent: z.string(),
  highlightChanges: z.boolean().default(true)
});

@Injectable()
export class VersionCompareService {
  async compareVersions(data: unknown) {
    const { oldContent, newContent, highlightChanges } = compareSchema.parse(data);
    
    const differences = diffLines(oldContent, newContent);
    
    if (!highlightChanges) {
      return differences;
    }

    return this.formatDifferences(differences);
  }

  private formatDifferences(differences: Change[]) {
    return differences.map(part => ({
      value: part.value,
      type: part.added ? 'added' : part.removed ? 'removed' : 'unchanged',
      lineNumber: part.count
    }));
  }

  async exportComparison(oldVersion: string, newVersion: string) {
    const differences = await this.compareVersions({
      oldContent: oldVersion,
      newContent: newVersion,
      highlightChanges: true
    });

    // Générer un rapport détaillé des modifications
    const report = differences.map(diff => {
      const prefix = diff.type === 'added' ? '+' : diff.type === 'removed' ? '-' : ' ';
      return `${prefix} ${diff.value}`;
    }).join('\n');

    return {
      report,
      stats: {
        added: differences.filter(d => d.type === 'added').length,
        removed: differences.filter(d => d.type === 'removed').length,
        unchanged: differences.filter(d => d.type === 'unchanged').length
      }
    };
  }
}

export type PeriodFilter = '7d' | '30d' | '90d' | '1y';

export type DeviceType = 'mobile' | 'tablet' | 'desktop';

export interface AnalyticsDashboardProps {
  period: PeriodFilter;
  onPeriodChange: (period: PeriodFilter) => void;
}

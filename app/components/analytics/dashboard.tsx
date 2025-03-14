import { useState } from 'react';
import { motion } from 'framer-motion';
import { useFetcher } from '@remix-run/react';
import { Button } from '~/components/ui/button';
import { Card } from '~/components/ui/card';
import { Tabs, TabsList, TabsTrigger } from '~/components/ui/tabs';
import { ArrowUp, ArrowDown, Monitor, Smartphone, Tablet } from 'lucide-react';
import type { PeriodFilter } from '~/types/analytics';

const periods: Array<{ value: PeriodFilter; label: string }> = [
  { value: '7d', label: '7 jours' },
  { value: '30d', label: '30 jours' },
  { value: '90d', label: '90 jours' },
  { value: '1y', label: '1 an' }
];

export function AnalyticsDashboard() {
  const [period, setPeriod] = useState<PeriodFilter>('30d');
  const fetcher = useFetcher();

  const { data } = fetcher.data || {};

  return (
    <div className="space-y-8">
      <Tabs value={period} onValueChange={(v) => setPeriod(v as PeriodFilter)}>
        <TabsList>
          {periods.map(({ value, label }) => (
            <TabsTrigger key={value} value={value}>
              {label}
            </TabsTrigger>
          ))}
        </TabsList>
      </Tabs>

      <div className="grid gap-4 md:grid-cols-3">
        {data && (
          <>
            <MetricCard
              title="Visites"
              value={data.current.visits}
              trend={data.trends.visits}
              previous={data.previous.visits}
            />
            <MetricCard
              title="Conversions"
              value={`${data.current.conversions}%`}
              trend={data.trends.conversions}
              previous={`${data.previous.conversions}%`}
            />
            <MetricCard
              title="Revenus"
              value={`${data.current.revenue}€`}
              trend={data.trends.revenue}
              previous={`${data.previous.revenue}€`}
            />
          </>
        )}
      </div>

      {data?.byDevice && (
        <Card className="p-6">
          <h3 className="text-lg font-medium mb-4">Par Appareil</h3>
          <div className="grid gap-4 md:grid-cols-3">
            <DeviceMetric
              icon={<Monitor className="h-5 w-5" />}
              label="Desktop"
              data={data.byDevice.desktop}
            />
            <DeviceMetric
              icon={<Smartphone className="h-5 w-5" />}
              label="Mobile" 
              data={data.byDevice.mobile}
            />
            <DeviceMetric
              icon={<Tablet className="h-5 w-5" />}
              label="Tablet"
              data={data.byDevice.tablet}
            />
          </div>
        </Card>
      )}
    </div>
  );
}

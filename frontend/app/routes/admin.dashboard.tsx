import { useEffect, useState } from 'react';
import { useNavigate } from '@remix-run/react';
import { Line } from 'react-chartjs-2';
import { io } from 'socket.io-client';
import { useUser } from '~/utils/user';
import { Card } from '~/components/ui/card';
import { Skeleton } from '~/components/ui/skeleton';

interface Stats {
  todayOrders: number;
  totalOrders: number;
  revenue: number;
  activeUsers: number;
}

export default function AdminDashboard() {
  const user = useUser();
  const navigate = useNavigate();
  const [stats, setStats] = useState<Stats | null>(null);
  const [historicalData, setHistoricalData] = useState<number[]>([]);

  useEffect(() => {
    if (!user?.isAdmin) {
      navigate('/login');
      return;
    }

    const socket = io(window.ENV.WS_URL, {
      auth: { token: user.token }
    });

    socket.on('connect', () => {
      socket.emit('getStats');
    });

    socket.on('statsUpdate', (data: Stats) => {
      setStats(data);
      setHistoricalData(prev => [...prev.slice(-11), data.revenue]);
    });

    return () => {
      socket.disconnect();
    };
  }, [user]);

  if (!stats) {
    return <StatsLoadingSkeleton />;
  }

  return (
    <div className="container mx-auto p-4 space-y-6">
      <h1 className="text-2xl font-bold mb-8">Tableau de Bord</h1>
      
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatsCard 
          title="Commandes aujourd'hui"
          value={stats.todayOrders}
          trend={10}
        />
        <StatsCard 
          title="Total commandes"
          value={stats.totalOrders}
        />
        <StatsCard 
          title="Chiffre d'affaires"
          value={`${stats.revenue.toLocaleString()} €`}
          trend={5}
        />
        <StatsCard 
          title="Utilisateurs actifs"
          value={stats.activeUsers}
        />
      </div>

      <Card className="p-4">
        <Line
          data={{
            labels: Array.from({length: 12}, (_, i) => `${11-i}min`).reverse(),
            datasets: [{
              label: 'Chiffre d\'affaires',
              data: historicalData,
              borderColor: 'rgb(99, 102, 241)',
              tension: 0.3
            }]
          }}
          options={{
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }}
        />
      </Card>
    </div>
  );
}

function StatsCard({ title, value, trend }: {
  title: string;
  value: string | number;
  trend?: number;
}) {
  return (
    <Card className="p-4">
      <h3 className="text-sm text-muted-foreground">{title}</h3>
      <p className="text-2xl font-bold mt-2">{value}</p>
      {trend && (
        <p className={`text-sm mt-2 ${trend > 0 ? 'text-green-500' : 'text-red-500'}`}>
          {trend > 0 ? '↑' : '↓'} {Math.abs(trend)}%
        </p>
      )}
    </Card>
  );
}

function StatsLoadingSkeleton() {
  return (
    <div className="container mx-auto p-4">
      <Skeleton className="h-8 w-48 mb-8" />
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {Array.from({length: 4}).map((_, i) => (
          <Card key={i} className="p-4">
            <Skeleton className="h-4 w-24 mb-4" />
            <Skeleton className="h-8 w-32" />
          </Card>
        ))}
      </div>
      <Card className="p-4 mt-6">
        <Skeleton className="h-[300px] w-full" />
      </Card>
    </div>
  );
}

import { useFetcher } from '@remix-run/react';
import { useEffect, useState } from 'react';
import { Button } from '~/components/ui/button';
import { Heart } from 'lucide-react';

interface FavoriteButtonProps {
  modelId: number;
  initialIsFavorite?: boolean;
  onToggle?: (isFavorite: boolean) => void;
}

export function FavoriteButton({ 
  modelId, 
  initialIsFavorite = false,
  onToggle 
}: FavoriteButtonProps) {
  const fetcher = useFetcher();
  const [isFavorite, setIsFavorite] = useState(initialIsFavorite);

  useEffect(() => {
    if (fetcher.data?.success) {
      setIsFavorite(fetcher.data.isFavorite);
      onToggle?.(fetcher.data.isFavorite);
    }
  }, [fetcher.data]);

  const handleToggle = () => {
    fetcher.submit(
      { modelId: String(modelId) },
      { 
        method: 'post',
        action: '/api/favorites'
      }
    );
  };

  return (
    <Button
      variant={isFavorite ? 'default' : 'outline'} 
      size="icon"
      onClick={handleToggle}
      disabled={fetcher.state === 'submitting'}
    >
      <Heart 
        className={isFavorite ? 'fill-current' : ''} 
        size={20} 
      />
    </Button>
  );
}

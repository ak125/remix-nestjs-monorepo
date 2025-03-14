import { useEffect, useRef } from 'react';
import { Dialog } from '@radix-ui/react-dialog';
import { useFetcher } from '@remix-run/react';
import { Loader2 } from 'lucide-react';

interface Props {
  pieceId: string;
  isOpen: boolean;
  onClose: () => void;
}

export function PieceTechnicalModal({ pieceId, isOpen, onClose }: Props) {
  const fetcher = useFetcher();
  const dialogRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (isOpen && pieceId) {
      fetcher.load(`/api/pieces/${pieceId}`);
    }
  }, [isOpen, pieceId]);

  const piece = fetcher.data;
  const isLoading = fetcher.state === 'loading';

  return (
    <Dialog.Root open={isOpen} onOpenChange={onClose}>
      <Dialog.Portal>
        <Dialog.Overlay className="fixed inset-0 bg-black/40" />
        <Dialog.Content ref={dialogRef} className="fixed left-1/2 top-1/2 w-full max-w-3xl -translate-x-1/2 -translate-y-1/2 space-y-4 rounded-lg bg-white p-6 shadow-xl">
          <Dialog.Title className="text-xl font-bold">
            Fiche technique
          </Dialog.Title>

          <div className="min-h-[400px]">
            {isLoading ? (
              <div className="flex h-full items-center justify-center">
                <Loader2 className="h-8 w-8 animate-spin" />
              </div>
            ) : piece ? (
              <div className="space-y-6">
                {/* Content */}
              </div>
            ) : null}
          </div>

          <Dialog.Close className="absolute right-4 top-4">
            ✕
          </Dialog.Close>
        </Dialog.Content>
      </Dialog.Portal>
    </Dialog.Root>
  );
}

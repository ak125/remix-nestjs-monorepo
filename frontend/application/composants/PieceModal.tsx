import { useState } from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@shadcn/ui/dialog";
import { Button } from "@shadcn/ui/button";
import { X } from "lucide-react";

export default function PieceModal() {
  const [pieceId, setPieceId] = useState<string | null>(null);

  return (
    <Dialog>
      <DialogTrigger asChild>
        <Button onClick={() => setPieceId("12345")} className="bg-primary text-white hover:bg-primary-dark">
          Voir détails
        </Button>
      </DialogTrigger>
      <DialogContent className="max-w-2xl">
        <DialogHeader>
          <DialogTitle>Fiche technique</DialogTitle>
          <Button variant="ghost" className="absolute right-4 top-4" onClick={() => setPieceId(null)}>
            <X className="w-5 h-5" />
          </Button>
        </DialogHeader>

        {/* Iframe dynamique */}
        {pieceId ? (
          <iframe
            src={`/fiche/${pieceId}`}
            width="100%"
            height="500"
            className="rounded-lg border shadow-sm"
          />
        ) : (
          <p className="text-center text-gray-500">Chargement...</p>
        )}
      </DialogContent>
    </Dialog>
  );
}

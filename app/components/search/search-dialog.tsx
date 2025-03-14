import { useState } from "react";
import { QuickSearch } from "./quick-search";
import { Button } from "~/components/ui/button";
import { SearchIcon } from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "~/components/ui/dialog";

export function SearchDialog() {
  const [open, setOpen] = useState(false);
  
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button variant="ghost" size="icon" aria-label="Recherche">
          <SearchIcon className="h-5 w-5" />
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[800px] p-0">
        <DialogHeader className="px-4 pt-5 pb-0">
          <DialogTitle>Recherche</DialogTitle>
        </DialogHeader>
        <div className="p-4 pt-0">
          <QuickSearch 
            onDismiss={() => setOpen(false)} 
            autoFocus={true}
          />
          
          <div className="mt-6 px-2">
            <h3 className="text-sm font-medium">Recherches populaires</h3>
            <div className="mt-2 flex flex-wrap gap-2">
              {["amortisseur", "filtre", "huile moteur", "batterie", "freins"].map((term) => (
                <Button 
                  key={term}
                  variant="secondary"
                  size="sm"
                  onClick={() => {
                    window.location.href = `/recherche?q=${encodeURIComponent(term)}`;
                    setOpen(false);
                  }}
                >
                  {term}
                </Button>
              ))}
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}

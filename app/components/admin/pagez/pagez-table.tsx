import { useState } from "react";
import { useFetcher } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "~/components/ui/dialog";
import { formatDate } from "~/lib/utils";
import { Pencil, AlertCircle, FileText } from "lucide-react";
import { PageZEditor } from "~/components/admin/pagez/pagez-editor";

interface PageZ {
  id: number;
  mfId: number;
  pgId: number;
  pgName: string;
  mfName: string;
  seoTitle?: string;
  seoDesc?: string;
  content?: string;
  lastModified: Date;
}

export function PageZTable({ pages }: { pages: PageZ[] }) {
  const [selectedPage, setSelectedPage] = useState<PageZ | null>(null);
  const [isEditing, setIsEditing] = useState(false);
  const fetcher = useFetcher();
  
  const handleOpenModal = (page: PageZ) => {
    setSelectedPage(page);
    setIsEditing(true);
  };
  
  const handleCloseModal = () => {
    setSelectedPage(null);
    setIsEditing(false);
  };
  
  return (
    <div className="space-y-6">
      <div className="border rounded-md overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-muted/50">
            <tr>
              <th className="px-4 py-3 text-left font-medium w-16">MF</th>
              <th className="px-4 py-3 text-left font-medium w-16">PG</th>
              <th className="px-4 py-3 text-left font-medium">Gamme</th>
              <th className="px-4 py-3 text-left font-medium w-44">Dernière MAJ</th>
              <th className="px-4 py-3 text-left font-medium w-32">SEO</th>
              <th className="px-4 py-3 text-center font-medium w-28">Action</th>
            </tr>
          </thead>
          <tbody className="divide-y">
            {pages.map((page) => (
              <tr key={page.id} className="hover:bg-muted/50">
                <td className="px-4 py-3">{page.mfId}</td>
                <td className="px-4 py-3">{page.pgId}</td>
                <td className="px-4 py-3">
                  <div className="font-medium">{page.pgName}</div>
                  <div className="text-muted-foreground text-xs">{page.mfName}</div>
                </td>
                <td className="px-4 py-3 text-sm text-muted-foreground">
                  {formatDate(page.lastModified)}
                </td>
                <td className="px-4 py-3">
                  <div className="flex items-center gap-2">
                    <div 
                      className={`w-3 h-3 rounded-full ${page.seoTitle && page.seoDesc ? 'bg-green-500' : 'bg-red-500'}`} 
                      title={page.seoTitle && page.seoDesc ? 'SEO complet' : 'SEO incomplet'}
                    />
                    <div 
                      className={`w-3 h-3 rounded-full ${page.content ? 'bg-green-500' : 'bg-yellow-500'}`}
                      title={page.content ? 'Contenu présent' : 'Contenu absent'}
                    />
                  </div>
                </td>
                <td className="px-4 py-3 text-center">
                  <Button 
                    variant="ghost" 
                    size="sm" 
                    onClick={() => handleOpenModal(page)}
                  >
                    <Pencil className="w-4 h-4 mr-2" />
                    Modifier
                  </Button>
                </td>
              </tr>
            ))}
            {pages.length === 0 && (
              <tr>
                <td colSpan={6} className="text-center py-8 text-muted-foreground">
                  <div className="flex flex-col items-center">
                    <AlertCircle className="w-6 h-6 mb-2" />
                    <p>Aucune page trouvée</p>
                  </div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
      
      {isEditing && selectedPage && (
        <Dialog open={isEditing} onOpenChange={setIsEditing}>
          <DialogContent className="max-w-4xl">
            <DialogHeader>
              <DialogTitle>
                <div className="flex items-center gap-2">
                  <FileText className="w-5 h-5" />
                  <span>Page Z #{selectedPage.id}: {selectedPage.pgName}</span>
                </div>
              </DialogTitle>
              <DialogDescription>
                Édition du contenu et référencement pour {selectedPage.mfName}
              </DialogDescription>
            </DialogHeader>
            
            <div className="mt-4">
              <PageZEditor page={selectedPage} onClose={handleCloseModal} />
            </div>
          </DialogContent>
        </Dialog>
      )}
    </div>
  );
}

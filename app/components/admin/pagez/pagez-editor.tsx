import { useState } from "react";
import { useFetcher } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Label } from "~/components/ui/label";
import { Textarea } from "~/components/ui/textarea";
import { Alert, AlertTitle, AlertDescription } from "~/components/ui/alert";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "~/components/ui/tabs";
import { Save, FileText, FileCode, Tag } from "lucide-react";

interface PageZ {
  id: number;
  mfId: number;
  pgId: number;
  pgName: string;
  mfName: string;
  seoTitle?: string;
  seoDesc?: string;
  content?: string;
}

interface PageZEditorProps {
  page: PageZ;
  onClose: () => void;
}

export function PageZEditor({ page, onClose }: PageZEditorProps) {
  const [formData, setFormData] = useState({
    id: page.id,
    mfId: page.mfId,
    pgId: page.pgId,
    pgName: page.pgName,
    mfName: page.mfName,
    seoTitle: page.seoTitle || '',
    seoDesc: page.seoDesc || '',
    content: page.content || ''
  });
  
  const fetcher = useFetcher();
  
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };
  
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    const formDataObj = new FormData();
    Object.entries(formData).forEach(([key, value]) => {
      formDataObj.append(key, value.toString());
    });
    
    fetcher.submit(formDataObj, {
      method: "POST",
      action: "/api/admin/pagez",
    });
  };
  
  const isSubmitting = fetcher.state === "submitting";
  const isSuccess = fetcher.data?.success;
  const error = fetcher.data?.error;
  
  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {isSuccess && (
        <Alert className="bg-green-50 border-green-200 text-green-800">
          <AlertTitle className="text-green-800">Sauvegarde réussie</AlertTitle>
          <AlertDescription>
            Les informations ont été mises à jour avec succès.
          </AlertDescription>
        </Alert>
      )}
      
      {error && (
        <Alert variant="destructive">
          <AlertTitle>Erreur</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      )}
      
      <Tabs defaultValue="seo">
        <TabsList className="grid grid-cols-2 w-[400px]">
          <TabsTrigger value="seo" className="flex items-center">
            <Tag className="w-4 h-4 mr-2" />
            SEO
          </TabsTrigger>
          <TabsTrigger value="content" className="flex items-center">
            <FileText className="w-4 h-4 mr-2" />
            Contenu
          </TabsTrigger>
        </TabsList>
        
        <TabsContent value="seo" className="space-y-4 pt-4">
          <div className="space-y-2">
            <Label htmlFor="seoTitle">Titre SEO</Label>
            <Input
              id="seoTitle"
              name="seoTitle"
              value={formData.seoTitle}
              onChange={handleInputChange}
              className="w-full"
              maxLength={160}
            />
            <p className="text-xs text-muted-foreground">
              {formData.seoTitle.length}/160 caractères recommandés
            </p>
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="seoDesc">Description SEO</Label>
            <Textarea
              id="seoDesc"
              name="seoDesc"
              value={formData.seoDesc}
              onChange={handleInputChange}
              className="w-full min-h-[100px]"
              maxLength={320}
            />
            <p className="text-xs text-muted-foreground">
              {formData.seoDesc.length}/320 caractères recommandés
            </p>
          </div>
        </TabsContent>
        
        <TabsContent value="content" className="space-y-4 pt-4">
          <div className="space-y-2">
            <Label htmlFor="content">Contenu HTML</Label>
            <Textarea
              id="content"
              name="content"
              value={formData.content}
              onChange={handleInputChange}
              className="font-mono text-sm w-full min-h-[400px]"
            />
            <p className="text-xs text-muted-foreground">
              Vous pouvez utiliser du HTML pour formater le contenu.
            </p>
          </div>
        </TabsContent>
      </Tabs>
      
      <div className="flex justify-end gap-3">
        <Button 
          type="button" 
          variant="outline" 
          onClick={onClose}
          disabled={isSubmitting}
        >
          Annuler
        </Button>
        <Button 
          type="submit" 
          disabled={isSubmitting}
          className="flex items-center"
        >
          <Save className="w-4 h-4 mr-2" />
          Sauvegarder
        </Button>
      </div>
    </form>
  );
}

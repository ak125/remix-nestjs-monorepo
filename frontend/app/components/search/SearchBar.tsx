import { Form } from "@remix-run/react";
import { Input } from "~/components/ui/input";
import { Button } from "~/components/ui/button";
import { Search } from "lucide-react";

interface SearchBarProps {
  defaultValue?: string;
}

export function SearchBar({ defaultValue = "" }: SearchBarProps) {
  return (
    <Form method="get" className="flex gap-2">
      <div className="relative flex-1">
        <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
        <Input
          type="search"
          name="q" 
          placeholder="Rechercher une pièce..."
          defaultValue={defaultValue}
          className="pl-9"
        />
      </div>
      <Button type="submit">
        Rechercher
      </Button>
    </Form>
  );
}

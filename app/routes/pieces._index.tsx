import { Breadcrumb } from "~/components/ui/breadcrumb";
import { useBreadcrumbs } from "~/hooks/use-breadcrumbs";

export default function PiecesIndexPage() {
  const breadcrumbs = useBreadcrumbs();
  
  return (
    <div className="space-y-6">
      <Breadcrumb items={breadcrumbs} className="mb-8" />
      
      {/* Rest of the page content */}
    </div>
  );
}

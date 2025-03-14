import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData, useSearchParams, Link } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Select } from "@/components/ui/select";
import { Pagination } from "@/components/ui/pagination";
import { RefundRow } from "@/components/admin/RefundRow";
import { useAdminRefunds } from "@/hooks/useAdminRefunds";
import { formatDate, formatPrice } from "@/lib/format";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const status = url.searchParams.get("status");
  const orderId = url.searchParams.get("orderId");
  const page = url.searchParams.get("page") || "1";

  const response = await fetch(
    `${process.env.API_URL}/admin/refunds?${new URLSearchParams({
      status: status || "",
      orderId: orderId || "",
      page,
    })}`,
    {
      headers: {
        Cookie: request.headers.get("Cookie") || "",
      },
    }
  );

  if (!response.ok) {
    throw new Response("Non autorisé", { status: 401 });
  }

  return json(await response.json());
};

export default function AdminRefunds() {
  const { refunds, pagination } = useLoaderData<typeof loader>();
  const [searchParams, setSearchParams] = useSearchParams();
  const { updateRefundStatus } = useAdminRefunds();

  return (
    <div className="container mx-auto p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Gestion des Remboursements</h1>
        
        <div className="flex gap-4">
          <Input
            placeholder="Rechercher par ID commande"
            value={searchParams.get("orderId") || ""}
            onChange={(e) => 
              setSearchParams({ orderId: e.target.value }, { replace: true })
            }
          />

          <Select
            value={searchParams.get("status") || ""}
            onValueChange={(value) =>
              setSearchParams({ status: value }, { replace: true })
            }
          >
            <option value="">Tous les statuts</option>
            <option value="PENDING">En attente</option>
            <option value="COMPLETED">Complété</option>
            <option value="REJECTED">Refusé</option>
          </Select>
        </div>
      </div>

      <Card>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b">
                <th className="p-4 text-left">Date</th>
                <th className="p-4 text-left">Commande</th>
                <th className="p-4 text-left">Client</th>
                <th className="p-4 text-right">Montant</th>
                <th className="p-4 text-center">Statut</th>
                <th className="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {refunds.map((refund) => (
                <RefundRow
                  key={refund.id}
                  refund={refund}
                  onUpdateStatus={updateRefundStatus}
                />
              ))}
            </tbody>
          </table>
        </div>
      </Card>

      <div className="mt-6 flex justify-center">
        <Pagination
          total={pagination.total}
          pageSize={20}
          currentPage={pagination.current}
          onPageChange={(page) =>
            setSearchParams({ page: String(page) }, { replace: true })
          }
        />
      </div>
    </div>
  );
}

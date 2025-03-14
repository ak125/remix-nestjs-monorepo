import { formatDate, formatPrice } from "@/lib/format";

interface Refund {
  id: string;
  amount: number;
  createdAt: string;
  reason?: string;
  status: string;
}

interface RefundsHistoryProps {
  refunds: Refund[];
  totalAmount: number;
  refundedAmount: number;
}

export default function RefundsHistory({ 
  refunds, 
  totalAmount,
  refundedAmount 
}: RefundsHistoryProps) {
  if (!refunds.length) return null;

  return (
    <div className="mt-4 border-t pt-4">
      <h3 className="font-medium mb-2">Historique des remboursements</h3>
      
      <div className="space-y-2">
        {refunds.map((refund) => (
          <div 
            key={refund.id}
            className="flex justify-between items-center text-sm"
          >
            <div className="text-gray-600">
              <p>Le {formatDate(refund.createdAt)}</p>
              {refund.reason && (
                <p className="text-xs">Motif : {refund.reason}</p>
              )}
            </div>
            <div className="text-orange-600 font-medium">
              {formatPrice(refund.amount)}
            </div>
          </div>
        ))}

        <div className="flex justify-between items-center pt-2 border-t text-sm">
          <div className="font-medium">Total remboursé</div>
          <div className="text-orange-600 font-medium">
            {formatPrice(refundedAmount)} / {formatPrice(totalAmount)}
          </div>
        </div>
      </div>
    </div>
  );
}

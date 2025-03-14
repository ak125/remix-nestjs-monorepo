import { useState } from "react";
import { Form, useActionData } from "@remix-run/react";
import { Input } from "~/components/ui/input";
import { Button } from "~/components/ui/button";
import { Alert, AlertDescription } from "~/components/ui/alert";
import { Loader2 } from "lucide-react";

interface SubscribeFormProps {
  showTitle?: boolean;
  compact?: boolean;
  className?: string;
}

export function SubscribeForm({ 
  showTitle = true, 
  compact = false,
  className = "" 
}: SubscribeFormProps) {
  const actionData = useActionData<{
    success: boolean;
    message: string;
  }>();
  const [isSubmitting, setIsSubmitting] = useState(false);

  return (
    <div className={className}>
      {showTitle && !compact && (
        <div className="mb-4">
          <h3 className="text-lg font-semibold">Newsletter</h3>
          <p className="text-sm text-muted-foreground">
            Restez informé des dernières nouveautés et promotions.
          </p>
        </div>
      )}

      <Form 
        method="post" 
        action="/api/newsletter"
        onSubmit={() => setIsSubmitting(true)}
        className="space-y-4"
      >
        <div className={compact ? "flex gap-2" : ""}>
          <Input
            type="email"
            name="email"
            placeholder="Votre email"
            required
            className={compact ? "flex-1" : "w-full mb-2"}
          />
          
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? (
              <Loader2 className="h-4 w-4 animate-spin mr-1" />
            ) : null}
            {compact ? "S'inscrire" : "Recevoir la newsletter"}
          </Button>
        </div>

        {actionData && (
          <Alert variant={actionData.success ? "default" : "destructive"}>
            <AlertDescription>{actionData.message}</AlertDescription>
          </Alert>
        )}
      </Form>
    </div>
  );
}

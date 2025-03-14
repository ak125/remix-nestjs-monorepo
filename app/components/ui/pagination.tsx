import * as React from "react";
import { cn } from "~/lib/utils";

export interface PaginationProps extends React.HTMLAttributes<HTMLDivElement> {}

const Pagination = React.forwardRef<HTMLDivElement, PaginationProps>(
  ({ className, ...props }, ref) => (
    <div
      ref={ref}
      className={cn("flex items-center justify-center space-x-2", className)}
      {...props}
    />
  )
);
Pagination.displayName = "Pagination";

export { Pagination };

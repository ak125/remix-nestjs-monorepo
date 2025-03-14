import * as React from "react"
import { cn } from "@/lib/utils"

export interface LoadingSpinnerProps extends React.HTMLAttributes<HTMLDivElement> {
  size?: "sm" | "default" | "lg"
}

const sizes = {
  sm: "h-4 w-4",
  default: "h-6 w-6",
  lg: "h-8 w-8",
}

export const LoadingSpinner = React.forwardRef<HTMLDivElement, LoadingSpinnerProps>(
  ({ className, size = "default", ...props }, ref) => {
    return (
      <div
        ref={ref}
        className={cn("animate-spin", sizes[size], className)}
        {...props}
      >
        <div className="h-full w-full rounded-full border-2 border-current border-t-transparent" />
      </div>
    )
  }
)
LoadingSpinner.displayName = "LoadingSpinner"

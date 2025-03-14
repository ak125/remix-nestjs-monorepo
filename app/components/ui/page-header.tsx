import React from "react";
import { cn } from "~/lib/utils";

interface PageHeaderProps {
  title: string;
  subtitle?: string;
  logo?: boolean;
  className?: string;
}

export function PageHeader({
  title,
  subtitle,
  logo = true,
  className
}: PageHeaderProps) {
  return (
    <div className={cn(
      "flex items-center justify-between px-4 py-6 border-b",
      className
    )}>
      {/* Titres */}
      <div className="flex-1">
        <h1 className="text-3xl font-bold">{title}</h1>
        {subtitle && (
          <h2 className="text-xl text-muted-foreground mt-1">{subtitle}</h2>
        )}
      </div>
      
      {/* Logo */}
      {logo && (
        <div className="flex justify-end">
          <img 
            src="/images/logo-icon.png" 
            alt="Logo" 
            className="h-16 w-auto" 
          />
        </div>
      )}
    </div>
  );
}

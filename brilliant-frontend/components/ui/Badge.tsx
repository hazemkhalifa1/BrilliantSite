import * as React from "react";
import { cn } from "@/lib/utils";

type BadgeVariant = "neutral" | "secondary" | "tertiary" | "outline" | "success";

const variantClasses: Record<BadgeVariant, string> = {
  neutral: "bg-neutral-light text-neutral",
  secondary: "bg-secondary/10 text-secondary",
  tertiary: "bg-tertiary text-white",
  outline: "border border-line text-neutral",
  success: "bg-green-600 text-white",
};

export interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement> {
  variant?: BadgeVariant;
}

export function Badge({ className, variant = "neutral", ...props }: BadgeProps) {
  return (
    <span
      className={cn(
        "inline-flex items-center px-2.5 py-0.5 font-headline text-xs font-semibold uppercase tracking-wide",
        variantClasses[variant],
        className,
      )}
      {...props}
    />
  );
}

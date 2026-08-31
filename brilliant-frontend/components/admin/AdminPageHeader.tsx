import * as React from "react";

export interface AdminPageHeaderProps {
  title: string;
  description?: string;
  action?: React.ReactNode;
}

export function AdminPageHeader({ title, description, action }: AdminPageHeaderProps) {
  return (
    <div className="flex flex-wrap items-center justify-between gap-4 border-b border-line pb-5">
      <div>
        <h1 className="font-headline text-2xl font-bold uppercase">{title}</h1>
        {description && <p className="mt-1 text-sm text-neutral/60">{description}</p>}
      </div>
      {action && <div className="flex items-center gap-2">{action}</div>}
    </div>
  );
}

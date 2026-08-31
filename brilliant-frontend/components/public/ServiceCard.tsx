import { Wrench, Cog, Building2, HardHat } from "lucide-react";
import type { Service } from "@/types";
import { truncate } from "@/lib/utils";

const ICONS = [Wrench, Cog, Building2, HardHat];

export function ServiceCard({ service, index = 0 }: { service: Service; index?: number }) {
  const Icon = ICONS[index % ICONS.length];
  return (
    <div className="card-brilliant group flex flex-col p-6 transition-colors hover:border-tertiary">
      <div className="flex items-center gap-3">
        <div className="flex h-11 w-11 items-center justify-center bg-secondary/10 text-secondary">
          <Icon className="h-6 w-6" />
        </div>
        <span className="font-headline text-xs font-semibold uppercase tracking-widest text-neutral/50">
          {service.categoryName}
        </span>
      </div>
      <h3 className="mt-4 font-headline text-lg font-bold uppercase">{service.title}</h3>
      <span className="mt-2 block h-[3px] w-8 bg-tertiary transition-all group-hover:w-16" aria-hidden="true" />
      <p className="mt-3 text-sm leading-relaxed text-neutral/70">
        {truncate(service.description, 140)}
      </p>
    </div>
  );
}

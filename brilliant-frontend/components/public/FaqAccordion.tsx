"use client";

import { useState } from "react";
import { Plus } from "lucide-react";
import { cn } from "@/lib/utils";

export interface FaqItem {
  q: string;
  a: string;
}

export function FaqAccordion({ items }: { items: FaqItem[] }) {
  const [openIndex, setOpenIndex] = useState<number>(0);

  return (
    <div className="neo-shadow divide-y-4 divide-black border-2 border-black bg-white">
      {items.map((item, index) => {
        const isOpen = openIndex === index;
        return (
          <div key={`${item.q}-${index}`} className="faq-item" style={{ transitionDelay: `${index * 90}ms` }}>
            <button
              type="button"
              onClick={() => setOpenIndex(isOpen ? -1 : index)}
              aria-expanded={isOpen}
              className="flex w-full items-center justify-between gap-6 px-6 py-5 text-start transition-colors hover:bg-surface-container/60 md:px-8"
            >
              <span
                className={cn(
                  "font-headline text-base font-bold uppercase tracking-tight transition-colors md:text-lg",
                  isOpen ? "text-tertiary" : "text-neutral",
                )}
              >
                {item.q}
              </span>
              <Plus
                className={cn(
                  "h-6 w-6 shrink-0 text-secondary transition-transform duration-300 ease-out",
                  isOpen && "rotate-45",
                )}
              />
            </button>
            <div
              className={cn(
                "grid transition-[grid-template-rows] duration-300 ease-out",
                isOpen ? "grid-rows-[1fr]" : "grid-rows-[0fr]",
              )}
            >
              <div className="overflow-hidden">
                <p className="px-6 pb-6 text-neutral/70 md:px-8">{item.a}</p>
              </div>
            </div>
          </div>
        );
      })}
    </div>
  );
}
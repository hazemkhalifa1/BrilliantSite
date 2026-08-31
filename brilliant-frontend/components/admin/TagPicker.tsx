"use client";

import { cn } from "@/lib/utils";
import { useTranslations } from "next-intl";

export interface TagPickerProps {
  options: { id: number; name: string }[];
  value: number[];
  onChange: (ids: number[]) => void;
  label?: string;
}

export function TagPicker({ options, value, onChange, label }: TagPickerProps) {
  const t = useTranslations("admin.tagPicker");

  function toggle(id: number) {
    onChange(value.includes(id) ? value.filter((item) => item !== id) : [...value, id]);
  }

  return (
    <div className="flex flex-col gap-2">
      {label && <span className="text-sm font-medium text-neutral">{label}</span>}
      {options.length === 0 ? (
        <p className="text-xs text-neutral/50">{t("empty")}</p>
      ) : (
        <div className="flex flex-wrap gap-2">
          {options.map((tag) => {
            const selected = value.includes(tag.id);
            return (
              <button
                key={tag.id}
                type="button"
                onClick={() => toggle(tag.id)}
                className={cn(
                  "border px-3 py-1.5 font-headline text-xs font-semibold uppercase tracking-wide transition-colors",
                  selected
                    ? "border-secondary bg-secondary text-white"
                    : "border-line text-neutral hover:bg-neutral-light",
                )}
              >
                {tag.name}
              </button>
            );
          })}
        </div>
      )}
    </div>
  );
}

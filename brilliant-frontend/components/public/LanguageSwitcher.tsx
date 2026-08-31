"use client";

import { useLocale } from "next-intl";
import { usePathname, useRouter } from "@/src/i18n/navigation";
import { cn } from "@/lib/utils";

export function LanguageSwitcher({ className }: { className?: string }) {
  const router = useRouter();
  const pathname = usePathname();
  const locale = useLocale();

  const switchTo = (nextLocale: "en" | "ar") => {
    if (nextLocale !== locale) {
      router.push(pathname, { locale: nextLocale });
    }
  };

  return (
    <div
      className={cn(
        "flex items-center gap-2 text-sm uppercase tracking-widest text-white",
        className,
      )}
    >
      <button
        type="button"
        onClick={() => switchTo("en")}
        className={cn(
          "font-headline transition-colors hover:text-[#BA1A1A]",
          locale === "en" && "font-bold text-[#BA1A1A]",
        )}
      >
        EN
      </button>
      <span className="opacity-40" aria-hidden="true">
        |
      </span>
      <button
        type="button"
        onClick={() => switchTo("ar")}
        className={cn(
          "font-headline transition-colors hover:text-[#BA1A1A]",
          locale === "ar" && "font-bold text-[#BA1A1A]",
        )}
      >
        AR
      </button>
    </div>
  );
}

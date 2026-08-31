"use client";

import { useMemo, useState } from "react";
import { useLocale, useTranslations } from "next-intl";
import { SlidersHorizontal } from "lucide-react";
import type { ProductBrand, ProductCategory } from "@/types";
import { localized } from "@/lib/localize";

interface ProductFiltersProps {
  brands: ProductBrand[];
  categories: ProductCategory[];
  selectedBrandId: number | null;
  selectedCategoryId: number | null;
}

export function ProductFilters({
  brands,
  categories,
  selectedBrandId,
  selectedCategoryId,
}: ProductFiltersProps) {
  const t = useTranslations("productFilters");
  const locale = useLocale();
  const [brand, setBrand] = useState<number | null>(selectedBrandId);
  const [category, setCategory] = useState<number | null>(selectedCategoryId);

  const visibleCategories = useMemo(
    () => (brand ? categories.filter((c) => c.brandId === brand) : categories),
    [brand, categories],
  );

  const apply = () => {
    const params = new URLSearchParams();
    if (brand) params.set("brandId", String(brand));
    if (category) params.set("categoryId", String(category));
    const qs = params.toString();
    window.location.href = qs ? `/products?${qs}` : "/products";
  };

  const checkboxClass =
    "h-4 w-4 rounded-none border-2 border-black bg-white text-black focus:ring-0 focus:ring-offset-0 checked:bg-black";

  return (
    <div>
      <h2 className="mb-6 flex items-center justify-between border-b-2 border-black pb-4 font-headline text-xl font-bold uppercase">
        {t("filters")}
        <SlidersHorizontal className="h-5 w-5 text-[#BA1A1A]" aria-hidden="true" />
      </h2>

      <div className="mb-8">
        <h3 className="mb-4 flex items-center gap-2 font-headline text-xs font-bold uppercase tracking-widest text-neutral/60">
          <span className="inline-block h-2 w-2 bg-[#BA1A1A]" aria-hidden="true" />
          {t("categories")}
        </h3>
        <div className="space-y-3 font-headline text-xs font-semibold uppercase tracking-wide">
          {visibleCategories.length > 0 ? (
            visibleCategories.map((c) => (
              <label key={c.id} className="group flex cursor-pointer items-center gap-3">
                <input
                  type="checkbox"
                  checked={category === c.id}
                  onChange={() => setCategory(category === c.id ? null : c.id)}
                  className={checkboxClass}
                />
                <span className="text-neutral transition-colors group-hover:text-[#BA1A1A]">{localized(locale, c.name, c.nameAr)}</span>
              </label>
            ))
          ) : (
            <p className="text-xs font-normal normal-case text-neutral/50">{t("noCategories")}</p>
          )}
        </div>
      </div>

      <div className="mb-8">
        <h3 className="mb-4 flex items-center gap-2 font-headline text-xs font-bold uppercase tracking-widest text-neutral/60">
          <span className="inline-block h-2 w-2 bg-[#BA1A1A]" aria-hidden="true" />
          {t("brands")}
        </h3>
        <div className="space-y-3 font-headline text-xs font-semibold uppercase tracking-wide">
          {brands.map((b) => (
            <label key={b.id} className="group flex cursor-pointer items-center gap-3">
              <input
                type="checkbox"
                checked={brand === b.id}
                onChange={() => setBrand(brand === b.id ? null : b.id)}
                className={checkboxClass}
              />
              <span className="text-neutral transition-colors group-hover:text-[#BA1A1A]">{localized(locale, b.name, b.nameAr)}</span>
            </label>
          ))}
        </div>
      </div>

      <button
        type="button"
        onClick={apply}
        className="w-full border-2 border-black bg-black py-3 font-headline text-xs font-bold uppercase tracking-widest text-white transition-all hover:bg-[#BA1A1A] active:translate-x-[2px] active:translate-y-[2px]"
      >
        {t("apply")}
      </button>
    </div>
  );
}

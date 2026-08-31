import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { ArrowRight, Box, FileText } from "lucide-react";
import type { Product } from "@/types";
import { getImageUrl } from "@/lib/utils";
import { localized } from "@/lib/localize";
import { SiteImage } from "@/components/ui/SiteImage";

export async function BrutalProductCard({ product }: { product: Product }) {
  const locale = await getLocale();
  const t = await getTranslations("productDetail");
  const tCommon = await getTranslations("common");
  return (
    <article className="brutalist-card group relative flex flex-col bg-white">
      <div
        className="absolute left-0 top-0 z-10 h-2 w-full border-b-2 border-black bg-[#BA1A1A]"
        aria-hidden="true"
      />
      <div className="relative aspect-square w-full overflow-hidden border-b-4 border-black bg-neutral-light">
        {product.imagePath ? (
          <SiteImage
            src={product.imagePath}
            alt={localized(locale, product.name, product.nameAr)}
            className="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
          />
        ) : (
          <div className="flex h-full w-full items-center justify-center">
            <Box className="h-16 w-16 text-neutral/25" strokeWidth={1.5} aria-hidden="true" />
          </div>
        )}
        <div className="absolute left-4 top-4 z-20 flex flex-col gap-2">
          {product.brandName && (
            <span className="border-2 border-black bg-[#0059BB] px-3 py-1 font-headline text-[10px] font-bold uppercase tracking-widest text-white">
              {product.brandName}
            </span>
          )}
          {product.categoryName && (
            <span className="flex items-center gap-1 border-2 border-black bg-white px-3 py-1 font-headline text-[10px] font-bold uppercase tracking-widest text-black">
              {product.categoryName}
            </span>
          )}
        </div>
      </div>
      <div className="flex flex-1 flex-col p-6">
        <div className="mb-4 flex items-start justify-between gap-2">
          <h3 className="pr-2 font-headline text-xl font-bold uppercase leading-tight text-neutral">
            {localized(locale, product.name, product.nameAr)}
          </h3>
          <ArrowRight
            className="h-6 w-6 shrink-0 text-neutral transition-colors group-hover:text-[#0059BB]"
            aria-hidden="true"
          />
        </div>
        <p className="mb-6 line-clamp-2 text-sm leading-relaxed text-neutral/70">
          {localized(locale, product.description || t("noDescription"), product.descriptionAr)}
        </p>
        <div className="mt-auto flex flex-col gap-2">
          <Link
            href={`/products/${product.id}`}
            className="flex w-full items-center justify-center gap-2 border-2 border-black bg-white py-3 font-headline text-xs font-bold uppercase tracking-widest text-neutral transition-colors hover:bg-[#0059BB] hover:text-white"
          >
            {tCommon("viewSpecs")}
            <ArrowRight className="h-4 w-4" aria-hidden="true" />
          </Link>
          {product.documentationUrl && (
            <a
              href={getImageUrl(product.documentationUrl)}
              target="_blank"
              rel="noopener noreferrer"
              className="flex w-full items-center justify-center gap-2 border-2 border-[#BA1A1A] bg-[#BA1A1A] py-3 font-headline text-xs font-bold uppercase tracking-widest text-white transition-opacity hover:opacity-90"
            >
              <FileText className="h-4 w-4" aria-hidden="true" />
              {t("downloadDocs")}
            </a>
          )}
        </div>
      </div>
    </article>
  );
}

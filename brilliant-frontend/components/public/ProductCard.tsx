import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { FileText, ArrowRight } from "lucide-react";
import type { Product } from "@/types";
import { getImageUrl, truncate } from "@/lib/utils";
import { SiteImage } from "@/components/ui/SiteImage";

export async function ProductCard({ product }: { product: Product }) {
  const t = await getTranslations("common");
  const tDetail = await getTranslations("productDetail");
  return (
    <div className="card-brilliant group flex flex-col overflow-hidden transition-colors hover:border-tertiary">
      <Link
        href={`/products/${product.id}`}
        className="relative block aspect-[4/3] overflow-hidden bg-neutral-light"
      >
        <SiteImage
          src={product.imagePath}
          alt={product.name}
          className="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
        />
      </Link>
      <div className="flex flex-1 flex-col p-4">
        <div className="flex items-center justify-between gap-2">
          <span className="font-headline text-xs font-semibold uppercase tracking-widest text-secondary">
            {product.categoryName}
          </span>
          {product.brandName && (
            <span className="font-headline text-xs uppercase tracking-widest text-neutral/50">
              {product.brandName}
            </span>
          )}
        </div>
        <Link
          href={`/products/${product.id}`}
          className="mt-2 font-headline text-lg font-bold uppercase transition-colors group-hover:text-secondary"
        >
          {product.name}
        </Link>
        <p className="mt-2 flex-1 text-sm leading-relaxed text-neutral/70">
          {truncate(product.description, 110)}
        </p>
        <div className="mt-4 flex flex-wrap items-center gap-3">
          <Link
            href={`/products/${product.id}`}
            className="inline-flex items-center gap-2 border border-secondary bg-secondary px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-opacity hover:opacity-90"
          >
            {t("viewDetails")}
          </Link>
          {product.documentationUrl && (
            <a
              href={getImageUrl(product.documentationUrl)}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-1.5 border border-[#BA1A1A] bg-[#BA1A1A] px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-opacity hover:opacity-90"
            >
              <FileText className="h-4 w-4" />
              {tDetail("downloadDocs")}
            </a>
          )}
        </div>
      </div>
      <span className="sr-only">{t("viewDetails")} {product.name}</span>
      <ArrowRight className="absolute h-0 w-0" aria-hidden="true" />
    </div>
  );
}

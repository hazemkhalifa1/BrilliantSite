import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { notFound } from "next/navigation";
import { ArrowLeft, Box, ChevronRight, FileText } from "lucide-react";
import { apiFetch } from "@/lib/api";
import { getImageUrl } from "@/lib/utils";
import type { PagedResult, Product } from "@/types";
import { localized } from "@/lib/localize";
import { SiteImage } from "@/components/ui/SiteImage";
// import { ProductCard } from "@/components/public/ProductCard";
import { BrutalProductCard } from "@/components/public/BrutalProductCard";

export const dynamic = "force-dynamic";

const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL || "https://brilliant-eng.com";

interface ProductPageProps {
  params: { id: string; locale: string };
}

async function getProduct(id: string): Promise<Product | null> {
  return apiFetch<Product>(`/products/${id}`).catch(() => null);
}

export async function generateMetadata({ params }: ProductPageProps): Promise<Metadata> {
  const t = await getTranslations({ locale: params.locale, namespace: "productDetail" });
  const product = await getProduct(params.id);
  if (!product) return { title: t("notFound") };
  const locale = await getLocale();
  return {
    title: localized(locale, product.name, product.nameAr),
    description: localized(locale, product.description, product.descriptionAr),
    openGraph: {
      title: localized(locale, product.name, product.nameAr),
      description: localized(locale, product.description, product.descriptionAr),
      images: [getImageUrl(product.imagePath)],
    },
  };
}

export default async function ProductPage({ params }: ProductPageProps) {
  const locale = await getLocale();
  const t = await getTranslations("productDetail");
  const product = await getProduct(params.id);
  if (!product) notFound();

  const backHref = product.brandId ? `/products?brandId=${product.brandId}` : "/products";
  const backLabel = product.brandName
    ? t("backToBrand", { brand: product.brandName })
    : t("backToAll");

  let related: Product[] = [];
  if (product.brandId) {
    related = await apiFetch<PagedResult<Product>>(
      `/products?onlyActive=true&brandId=${product.brandId}&pageSize=12&pageIndex=1`,
    )
      .then((data) => data.items.filter((item) => item.id !== product.id).slice(0, 3))
      .catch(() => [] as Product[]);
  }

  return (
    <article>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Product",
            name: localized(locale, product.name, product.nameAr),
            description: localized(locale, product.description, product.descriptionAr) || undefined,
            image: getImageUrl(product.imagePath),
            category: product.categoryName || undefined,
            brand: product.brandName
              ? { "@type": "Brand", name: product.brandName }
              : undefined,
            url: `${SITE_URL}/products/${product.id}`,
          }),
        }}
      />

      <section className="relative overflow-hidden bg-[#111C2D] py-16 text-white md:py-20">
        {product.imagePath && (
          <>
            <div
              className="absolute inset-0 bg-cover bg-center"
              style={{ backgroundImage: `url('${getImageUrl(product.imagePath)}')` }}
            />
            <div className="absolute inset-0 bg-[#111C2D]/80" />
          </>
        )}
        <div className="container-brilliant relative z-10">
          <Link
            href={backHref}
            className="inline-flex items-center gap-2 bg-[#BA1A1A] px-5 py-2.5 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-all hover:opacity-90"
          >
            <ArrowLeft className="h-4 w-4" />
            {backLabel}
          </Link>

          <div className="mt-8 flex flex-wrap items-center gap-2">
            <span className="bg-[#0059BB] border-[#0059BB] px-3 py-1 font-headline text-xs font-semibold uppercase tracking-widest text-white">
              {product.categoryName || t("badgeFallback")}
            </span>
            {product.brandName && (
              <span className="bg-[#BA1A1A] px-3 py-1 font-headline text-xs font-semibold uppercase tracking-widest text-white">
                {product.brandName}
              </span>
            )}
          </div>

          <h1 className="mt-5 max-w-3xl font-headline text-4xl font-bold uppercase leading-tight md:text-5xl">
            {localized(locale, product.name, product.nameAr)}
          </h1>
          <span className="mt-5 block h-[3px] w-16 bg-[#BA1A1A]" aria-hidden="true" />
        </div>
      </section>

      <div
        className="relative"
        style={{
          backgroundColor: "#ffffff",
          backgroundImage:
            "linear-gradient(to right, rgba(0,0,0,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,0.04) 1px, transparent 1px)",
          backgroundSize: "40px 40px",
        }}
      >
        <div className="container-brilliant py-12 md:py-16">
          <nav
            aria-label="Breadcrumb"
            className="mb-10 inline-flex flex-wrap items-center gap-2 border-2 border-black bg-white px-4 py-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60 shadow-[4px_4px_0px_black]"
          >
            <Link href="/products" className="transition-colors hover:text-[#0059BB] hover:underline">
              {t("breadcrumb")}
            </Link>
            {product.categoryName && (
              <>
                <ChevronRight className="h-3.5 w-3.5" aria-hidden="true" />
                <Link
                  href={
                    product.brandId && product.categoryId
                      ? `/products?brandId=${product.brandId}&categoryId=${product.categoryId}`
                      : `/products?categoryId=${product.categoryId}`
                  }
                  className="transition-colors hover:text-[#0059BB] hover:underline"
                >
                  {product.categoryName}
                </Link>
              </>
            )}
            <ChevronRight className="h-3.5 w-3.5" aria-hidden="true" />
            <span className="text-neutral ">{localized(locale, product.name, product.nameAr)}</span>
          </nav>

          <div className="grid grid-cols-1 items-start gap-12 lg:grid-cols-2">
            <div className="group relative">
              <div className="relative z-10 border-4 border-black bg-white p-4 shadow-[6px_6px_0px_black]">
                <span className="absolute start-4 top-4 z-20 border-2 border-black bg-[#BA1A1A] px-3 py-1 font-headline text-[10px] font-bold uppercase tracking-widest text-white">
                  {product.brandName || t("badgeFallback")}
                </span>
                <div className="relative aspect-[4/3] w-full overflow-hidden border-2 border-black bg-neutral-light">
                  {product.imagePath ? (
                    <SiteImage
                      src={product.imagePath}
                      alt={localized(locale, product.name, product.nameAr)}
                      className="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105 group-hover:grayscale"
                      eager
                    />
                  ) : (
                    <div className="flex h-full w-full items-center justify-center">
                      <Box className="h-20 w-20 text-neutral/25" strokeWidth={1.5} aria-hidden="true" />
                    </div>
                  )}
                </div>
                <div className="absolute left-0 top-0 h-8 w-8 border-l-4 border-t-4 border-black" aria-hidden="true" />
                <div className="absolute bottom-0 right-0 h-8 w-8 border-b-4 border-r-4 border-black" aria-hidden="true" />
              </div>
            </div>

            <div className="flex flex-col gap-8">
              <div>
                <h1 className="mb-4 font-headline text-4xl font-bold uppercase leading-none text-neutral md:text-5xl">
                  {localized(locale, product.name, product.nameAr)}
                </h1>
                <p className="border-s-4 border-[#0059BB] ps-4 text-lg leading-relaxed text-neutral/70">
                  {localized(locale, product.description || t("noDescription"), product.descriptionAr)}
                </p>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <Link
                  href={backHref}
                  className="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]"
                >
                  <span className="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60">
                    {t("brand")}
                  </span>
                  <span className="font-headline text-lg font-bold uppercase text-neutral md:text-xl">
                    {product.brandName || "—"}
                  </span>
                </Link>
                <div
                  className="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]"
                >
                  <span className="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60">
                    {t("category")}
                  </span>
                  <span className="font-headline text-lg font-bold uppercase text-neutral md:text-xl">
                    {product.categoryName || "—"}
                  </span>
                </div>
                {product.documentationUrl ? (
                  <a
                    href={getImageUrl(product.documentationUrl)}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]"
                  >
                    <span className="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60">
                      {t("documentation")}
                    </span>
                    <span className="flex items-center gap-2 font-headline text-lg font-bold uppercase text-[#0059BB] md:text-xl">
                      <FileText className="h-5 w-5" aria-hidden="true" />
                      {t("downloadDocs")}
                    </span>
                  </a>
                ) : (
                  <div className="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]">
                    <span className="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60">
                      {t("documentation")}
                    </span>
                    <span className="font-headline text-lg font-bold uppercase text-neutral/40 md:text-xl">
                      {t("notAvailable")}
                    </span>
                  </div>
                )}
                <Link
                  href="/products"
                  className="flex flex-col border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#BA1A1A] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#BA1A1A]"
                >
                  <span className="mb-2 font-headline text-[10px] font-bold uppercase tracking-widest text-neutral/60">
                    {t("catalog")}
                  </span>
                  <span className="font-headline text-lg font-bold uppercase text-neutral md:text-xl">
                    {t("backToAll")}
                  </span>
                </Link>
              </div>
            </div>
          </div>

          <div className="my-12 w-full border-t-4 border-black" aria-hidden="true" />
        </div>

        {related.length > 0 && (
          <section className="bg-neutral-light py-16">
            <div className="container-brilliant">
              <h2 className="font-headline text-xl font-bold uppercase">
                {t("moreFrom", { brand: product.brandName })}
              </h2>
              <span className="mt-2 block h-[3px] w-10 bg-[#0059BB]" aria-hidden="true" />
              <div className="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {related.map((item) => (
                  <BrutalProductCard key={item.id} product={item} />
                ))}
              </div>
            </div>
          </section>
        )}
      </div>
    </article>
  );
}

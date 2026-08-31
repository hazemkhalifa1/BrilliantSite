import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import Image from "next/image";
import { ArrowLeft, Box, ChevronLeft, ChevronRight } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { PagedResult, Product, ProductBrand, ProductCategory } from "@/types";
import { localized } from "@/lib/localize";
import { cn, getImageUrl } from "@/lib/utils";
import { PageHeader } from "@/components/public/PageHeader";
import { ProductFilters } from "@/components/public/ProductFilters";
import { BrutalProductCard } from "@/components/public/BrutalProductCard";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "products" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
  };
}

const PAGE_SIZE = 12;

async function getProductsData() {
  const [brands, categories, products] = await Promise.all([
    apiFetch<PagedResult<ProductBrand>>("/product-brands?onlyActive=true&pageSize=50")
      .then((data) => data.items)
      .catch(() => [] as ProductBrand[]),
    apiFetch<PagedResult<ProductCategory>>("/product-categories?onlyActive=true&pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ProductCategory[]),
    apiFetch<PagedResult<Product>>("/products?onlyActive=true&pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as Product[]),
  ]);

  const firstProductImageByBrand = new Map<number, string>();
  for (const product of products) {
    if (product.imagePath && product.brandId && !firstProductImageByBrand.has(product.brandId)) {
      firstProductImageByBrand.set(product.brandId, product.imagePath);
    }
  }

  return { brands, categories, firstProductImageByBrand };
}

interface ProductsPageProps {
  searchParams: { brandId?: string; categoryId?: string; page?: string };
}

export default async function ProductsPage({ searchParams }: ProductsPageProps) {
  const locale = await getLocale();
  const t = await getTranslations("products");
  const { brands, categories, firstProductImageByBrand } = await getProductsData();

  const brandId =
    searchParams.brandId && !Number.isNaN(Number(searchParams.brandId))
      ? Number(searchParams.brandId)
      : null;
  const categoryId =
    searchParams.categoryId && !Number.isNaN(Number(searchParams.categoryId))
      ? Number(searchParams.categoryId)
      : null;
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;

  const selectedBrand = brands.find((brand) => brand.id === brandId) ?? null;
  const isProductsView = Boolean(selectedBrand || categoryId);

  const makeUrl = (params: { brandId?: number | null; categoryId?: number | null; page?: number }) => {
    const queryParams = new URLSearchParams();
    if (params.brandId) queryParams.set("brandId", String(params.brandId));
    if (params.categoryId) queryParams.set("categoryId", String(params.categoryId));
    if (params.page && params.page > 1) queryParams.set("page", String(params.page));
    const qs = queryParams.toString();
    return qs ? `/products?${qs}` : "/products";
  };

  if (!isProductsView) {
    return (
      <div>
        <PageHeader eyebrow={t("eyebrow")} title={t("title")} subtitle={t("subtitle")} backgroundImage="https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80" />

        <section className="py-16 md:py-20">
          <div className="container-brilliant">
            {brands.length > 0 ? (
              <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                {brands.map((brand) => {
                  const brandImage =
                    brand.backgroundImagePath || firstProductImageByBrand.get(brand.id) || null;
                  return (
                    <Link
                      key={brand.id}
                      href={makeUrl({ brandId: brand.id, categoryId: null, page: 1 })}
                      className="group block"
                    >
                      <div className="overflow-hidden border-2 border-black bg-white shadow-[4px_4px_0px_black] transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:border-[#BA1A1A] hover:shadow-[6px_6px_0px_#BA1A1A]">
                        <div className="relative aspect-[16/10] bg-neutral-light">
                          {brandImage ? (
                            <Image
                              src={getImageUrl(brandImage)}
                              alt={localized(locale, brand.name, brand.nameAr)}
                              fill
                              sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                              className="object-contain transition-transform duration-300 group-hover:scale-105"
                            />
                          ) : (
                            <div className="flex h-full w-full items-center justify-center">
                              <Box className="h-16 w-16 text-neutral/25" strokeWidth={1.5} aria-hidden="true" />
                            </div>
                          )}
                          <span className="absolute end-3 top-3 bg-[#0059BB] px-2 py-0.5 font-headline text-[10px] font-bold uppercase tracking-widest text-white">
                            {t("viewProducts")}
                          </span>
                        </div>
                        <div className="p-4">
                          <h3 className="font-headline text-lg font-bold uppercase tracking-tight">
                            {localized(locale, brand.name, brand.nameAr)}
                          </h3>
                          {brand.description && (
                            <p className="mt-1 line-clamp-2 text-sm text-neutral/70">{localized(locale, brand.description, brand.descriptionAr)}</p>
                          )}
                        </div>
                      </div>
                    </Link>
                  );
                })}
              </div>
            ) : (
              <p className="text-center text-neutral/60">{t("noBrands")}</p>
            )}
          </div>
        </section>
      </div>
    );
  }

  const query = new URLSearchParams({
    onlyActive: "true",
    pageSize: String(PAGE_SIZE),
    pageIndex: String(pageIndex),
  });
  if (categoryId) query.set("categoryId", String(categoryId));
  else if (selectedBrand) query.set("brandId", String(selectedBrand.id));

  const result = await apiFetch<PagedResult<Product>>(`/products?${query.toString()}`)
    .catch(() => ({ items: [] as Product[], totalCount: 0, pageIndex: 1, pageSize: PAGE_SIZE }));

  const totalPages = Math.max(1, Math.ceil(result.totalCount / PAGE_SIZE));

  const paginationClass =
    "flex h-12 w-12 items-center justify-center border-2 border-black bg-white text-neutral shadow-[4px_4px_0px_black] transition-all hover:bg-neutral hover:text-white active:translate-x-[2px] active:translate-y-[2px] active:shadow-none";

  return (
    <div>
      <section className="relative overflow-hidden bg-[#111C2D] py-24 text-white md:py-28">
        <div
          className="absolute inset-0 bg-cover bg-center"
          style={{
            backgroundImage:
              "url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')",
          }}
        />
        <div className="absolute inset-0 bg-[#111C2D]/80" />
        <div className="container-brilliant relative z-10">
          <Link
            href="/products"
            className="inline-flex items-center gap-2 bg-[#BA1A1A] px-5 py-2.5 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-all hover:opacity-90"
          >
            <ArrowLeft className="h-4 w-4" />
            {t("backToAll")}
          </Link>
          <h1 className="mt-6 font-headline text-4xl font-bold uppercase tracking-tight md:text-5xl">
            {localized(locale, selectedBrand?.name ?? t("titleFallback"), selectedBrand?.nameAr)}
          </h1>
          {selectedBrand?.description && (
            <p className="mt-4 max-w-2xl text-lg text-white/70">{localized(locale, selectedBrand.description, selectedBrand.descriptionAr)}</p>
          )}
        </div>
      </section>

      <section className="py-12 md:py-16">
        <div className="container-brilliant">
          <div className="grid grid-cols-1 gap-8 md:grid-cols-12">
            <aside className="h-fit border-2 border-black bg-white p-6 shadow-[4px_4px_0px_black] md:col-span-3">
              <ProductFilters
                brands={brands}
                categories={categories}
                selectedBrandId={selectedBrand?.id ?? null}
                selectedCategoryId={categoryId}
              />
            </aside>

            <div className="md:col-span-9">
              {result.items.length > 0 ? (
                <div className="grid gap-8 sm:grid-cols-2 xl:grid-cols-3">
                  {result.items.map((product) => (
                    <BrutalProductCard key={product.id} product={product} />
                  ))}
                </div>
              ) : (
                <p className="mt-12 text-center text-neutral/60">{t("noProducts")}</p>
              )}

              {totalPages > 1 && (
                <div className="mt-16 flex flex-wrap justify-center gap-4">
                  <Link
                    href={makeUrl({ brandId: selectedBrand?.id, categoryId, page: pageIndex - 1 })}
                    aria-label={t("prevPage")}
                    className={cn(paginationClass, pageIndex <= 1 && "pointer-events-none opacity-40")}
                  >
                    <ChevronLeft className="h-5 w-5" />
                  </Link>
                  {Array.from({ length: totalPages }, (_, index) => index + 1).map((page) => (
                    <Link
                      key={page}
                      href={makeUrl({ brandId: selectedBrand?.id, categoryId, page })}
                      aria-current={page === pageIndex ? "page" : undefined}
                      className={cn(
                        "flex h-12 w-12 items-center justify-center border-2 font-headline text-sm font-bold transition-all",
                        page === pageIndex
                          ? "border-black bg-black text-white shadow-[4px_4px_0px_black]"
                          : paginationClass,
                      )}
                    >
                      {page}
                    </Link>
                  ))}
                  <Link
                    href={makeUrl({ brandId: selectedBrand?.id, categoryId, page: pageIndex + 1 })}
                    aria-label={t("nextPage")}
                    className={cn(paginationClass, pageIndex >= totalPages && "pointer-events-none opacity-40")}
                  >
                    <ChevronRight className="h-5 w-5" />
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}

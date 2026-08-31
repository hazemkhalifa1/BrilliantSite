import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { Plus } from "lucide-react";
import { adminFetchList } from "@/lib/adminApi";
import type { ProductBrand, ProductCategory } from "@/types";
import { AdminPageHeader } from "@/components/admin/AdminPageHeader";
import { AdminTable, type Column } from "@/components/admin/AdminTable";
import { DeleteButton } from "@/components/admin/DeleteButton";
import { Badge } from "@/components/ui/Badge";
import { Pagination } from "@/components/ui/Pagination";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "admin.sections.productCategories" });
  return { title: t("title") };
}

const PAGE_SIZE = 10;

interface ProductCategoriesAdminPageProps {
  searchParams: { page?: string; brandId?: string };
}

export default async function ProductCategoriesAdminPage({ searchParams }: ProductCategoriesAdminPageProps) {
  const t = await getTranslations("admin.sections.productCategories");
  const common = await getTranslations("common");
  const list = await getTranslations("admin.list");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;
  const brandId =
    searchParams.brandId && !Number.isNaN(Number(searchParams.brandId)) ? Number(searchParams.brandId) : null;

  const query = new URLSearchParams({
    pageIndex: String(pageIndex),
    pageSize: String(PAGE_SIZE),
    onlyActive: "false",
  });
  if (brandId) query.set("brandId", String(brandId));

  const [data, brands] = await Promise.all([
    adminFetchList<ProductCategory>(`/product-categories?${query.toString()}`).catch(() => ({
      items: [],
      totalCount: 0,
    })),
    adminFetchList<ProductBrand>("/product-brands?pageSize=100").catch(() => ({ items: [], totalCount: 0 })),
  ]);

  const filterUrl = (selected: number | null, page = 1) => {
    const params = new URLSearchParams();
    if (selected !== null) params.set("brandId", String(selected));
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `/admin/product-categories?${qs}` : "/admin/product-categories";
  };

  const columns: Column<ProductCategory>[] = [
    {
      key: "name",
      header: t("headers.category"),
      cell: (category) => (
        <Link
          href={`/admin/product-categories/${category.id}`}
          className="font-semibold text-neutral transition-colors hover:text-secondary"
        >
          {category.name}
        </Link>
      ),
    },
    { key: "brand", header: t("headers.brand"), cell: (category) => category.brandName || "—" },
    {
      key: "status",
      header: t("headers.status"),
      cell: (category) => (
        <Badge variant={category.isActive ? "success" : "neutral"}>
          {category.isActive ? common("active") : common("inactive")}
        </Badge>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <AdminPageHeader
        title={t("title")}
        description={t("description")}
        action={
          <Link href="/admin/product-categories/new" className="btn-primary !px-4 !py-2">
            <Plus className="h-4 w-4" />
            {t("new")}
          </Link>
        }
      />

      <div className="flex flex-wrap items-center gap-2">
        <Link
          href="/admin/product-categories"
          className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
            !brandId ? "border-primary bg-primary text-white" : "border-line text-neutral hover:bg-neutral-light"
          }`}
        >
          {list("all")}
        </Link>
        {brands.items.map((brand) => (
          <Link
            key={brand.id}
            href={filterUrl(brand.id)}
            className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
              brandId === brand.id
                ? "border-primary bg-primary text-white"
                : "border-line text-neutral hover:bg-neutral-light"
            }`}
          >
            {brand.name}
          </Link>
        ))}
      </div>

      <AdminTable
        columns={columns}
        data={data.items}
        rowKey={(category) => category.id}
        emptyMessage={list("empty", { entity: t("entity") })}
        actionsLabel={list("actions")}
        actions={(category) => (
          <div className="flex justify-end gap-2">
            <Link
              href={`/admin/product-categories/${category.id}`}
              className="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"
            >
              {list("edit")}
            </Link>
            <DeleteButton
              endpoint={`/product-categories/${category.id}`}
              title={list("deleteTitle", { entity: t("entity") })}
              message={list("deleteConfirm", { name: category.name })}
            />
          </div>
        )}
      />

      <Pagination
        pageIndex={pageIndex}
        totalCount={data.totalCount}
        pageSize={PAGE_SIZE}
        basePath="/admin/product-categories"
        queryParams={{ brandId: brandId ?? undefined }}
      />
    </div>
  );
}

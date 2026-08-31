import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { Plus } from "lucide-react";
import { adminFetchList } from "@/lib/adminApi";
import type { Product, ProductCategory } from "@/types";
import { truncate } from "@/lib/utils";
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
  const t = await getTranslations({ locale, namespace: "admin.sections.products" });
  return { title: t("title") };
}

const PAGE_SIZE = 10;

interface ProductsAdminPageProps {
  searchParams: { page?: string; categoryId?: string; search?: string };
}

export default async function ProductsAdminPage({ searchParams }: ProductsAdminPageProps) {
  const t = await getTranslations("admin.sections.products");
  const common = await getTranslations("common");
  const list = await getTranslations("admin.list");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;
  const categoryId =
    searchParams.categoryId && !Number.isNaN(Number(searchParams.categoryId))
      ? Number(searchParams.categoryId)
      : null;
  const search = searchParams.search?.trim() ?? "";

  const query = new URLSearchParams({
    pageIndex: String(pageIndex),
    pageSize: String(PAGE_SIZE),
    onlyActive: "false",
  });
  if (categoryId) query.set("categoryId", String(categoryId));
  if (search) query.set("search", search);

  const [data, categories] = await Promise.all([
    adminFetchList<Product>(`/products?${query.toString()}`).catch(() => ({ items: [], totalCount: 0 })),
    adminFetchList<ProductCategory>("/product-categories?pageSize=100").catch(() => ({
      items: [],
      totalCount: 0,
    })),
  ]);

  const filterUrl = (selected: number | null, page = 1) => {
    const params = new URLSearchParams();
    if (selected !== null) params.set("categoryId", String(selected));
    if (search) params.set("search", search);
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `/admin/products?${qs}` : "/admin/products";
  };

  const columns: Column<Product>[] = [
    {
      key: "name",
      header: t("headers.product"),
      cell: (product) => (
        <div>
          <Link
            href={`/admin/products/${product.id}`}
            className="font-semibold text-neutral transition-colors hover:text-secondary"
          >
            {product.name}
          </Link>
          <p className="mt-0.5 text-xs text-neutral/50">{truncate(product.description, 70)}</p>
        </div>
      ),
    },
    { key: "category", header: t("headers.category"), cell: (product) => product.categoryName || "—" },
    { key: "brand", header: t("headers.brand"), cell: (product) => product.brandName || "—" },
    { key: "order", header: t("headers.order"), cell: (product) => product.order ?? "—" },
    {
      key: "status",
      header: t("headers.status"),
      cell: (product) => (
        <Badge variant={product.isActive ? "success" : "neutral"}>
          {product.isActive ? common("active") : common("inactive")}
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
          <Link href="/admin/products/new" className="btn-primary !px-4 !py-2">
            <Plus className="h-4 w-4" />
            {t("new")}
          </Link>
        }
      />

      <div className="flex flex-wrap items-center justify-between gap-3">
        <div className="flex flex-wrap items-center gap-2">
          <Link
            href={search ? `/admin/products?search=${encodeURIComponent(search)}` : "/admin/products"}
            className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
              !categoryId ? "border-primary bg-primary text-white" : "border-line text-neutral hover:bg-neutral-light"
            }`}
          >
            {list("all")}
          </Link>
          {categories.items.map((category) => (
            <Link
              key={category.id}
              href={filterUrl(category.id)}
              className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
                categoryId === category.id
                  ? "border-primary bg-primary text-white"
                  : "border-line text-neutral hover:bg-neutral-light"
              }`}
            >
              {category.name}
            </Link>
          ))}
        </div>

        <form
          method="get"
          action={`/admin/products${categoryId ? `?categoryId=${categoryId}` : ""}`}
          className="flex items-center gap-2"
        >
          <input
            type="search"
            name="search"
            defaultValue={search}
            placeholder={list("searchPlaceholder", { entity: t("entity") })}
            className="w-56 border border-line bg-white px-3 py-2 text-sm text-neutral outline-none transition-colors focus:border-secondary"
          />
          <button
            type="submit"
            className="border border-primary bg-primary px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-colors hover:bg-neutral"
          >
            {list("search")}
          </button>
          {search && (
            <Link
              href={categoryId ? `/admin/products?categoryId=${categoryId}` : "/admin/products"}
              className="border border-line px-3 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:bg-neutral-light"
            >
              {list("clear")}
            </Link>
          )}
        </form>
      </div>

      <AdminTable
        columns={columns}
        data={data.items}
        rowKey={(product) => product.id}
        emptyMessage={list("empty", { entity: t("entity") })}
        actionsLabel={list("actions")}
        actions={(product) => (
          <div className="flex justify-end gap-2">
            <Link
              href={`/admin/products/${product.id}`}
              className="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"
            >
              {list("edit")}
            </Link>
            <DeleteButton
              endpoint={`/products/${product.id}`}
              title={list("deleteTitle", { entity: t("entity") })}
              message={list("deleteConfirm", { name: product.name })}
            />
          </div>
        )}
      />

      <Pagination
        pageIndex={pageIndex}
        totalCount={data.totalCount}
        pageSize={PAGE_SIZE}
        basePath="/admin/products"
        queryParams={{ categoryId: categoryId ?? undefined, search: search || undefined }}
      />
    </div>
  );
}

import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import Image from "next/image";
import { Plus } from "lucide-react";
import { adminFetchList } from "@/lib/adminApi";
import type { ProductBrand } from "@/types";
import { getImageUrl, truncate } from "@/lib/utils";
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
  const t = await getTranslations({ locale, namespace: "admin.sections.productBrands" });
  return { title: t("title") };
}

const PAGE_SIZE = 10;

interface ProductBrandsAdminPageProps {
  searchParams: { page?: string };
}

export default async function ProductBrandsAdminPage({ searchParams }: ProductBrandsAdminPageProps) {
  const t = await getTranslations("admin.sections.productBrands");
  const common = await getTranslations("common");
  const list = await getTranslations("admin.list");
  const upload = await getTranslations("admin.upload");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;

  const query = new URLSearchParams({
    pageIndex: String(pageIndex),
    pageSize: String(PAGE_SIZE),
    onlyActive: "false",
  });

  const data = await adminFetchList<ProductBrand>(`/product-brands?${query.toString()}`).catch(() => ({
    items: [],
    totalCount: 0,
  }));

  const columns: Column<ProductBrand>[] = [
    {
      key: "name",
      header: t("headers.brand"),
      cell: (brand) => (
        <div className="flex items-center gap-3">
          {brand.backgroundImagePath ? (
            <div className="relative h-14 w-20 shrink-0 overflow-hidden border border-line bg-white">
              <Image
                src={getImageUrl(brand.backgroundImagePath)}
                alt={brand.name}
                fill
                sizes="80px"
                className="object-cover"
              />
            </div>
          ) : (
            <div className="flex h-14 w-20 shrink-0 items-center justify-center border border-dashed border-line bg-neutral-light text-xs text-neutral/50">
              {upload("noImage")}
            </div>
          )}
          <Link
            href={`/admin/product-brands/${brand.id}`}
            className="font-semibold text-neutral transition-colors hover:text-secondary"
          >
            {brand.name}
          </Link>
          {brand.description && (
            <p className="mt-0.5 text-xs text-neutral/50">{truncate(brand.description, 60)}</p>
          )}
        </div>
      ),
    },
    { key: "order", header: t("headers.order"), cell: (brand) => brand.order ?? "—" },
    {
      key: "status",
      header: t("headers.status"),
      cell: (brand) => (
        <Badge variant={brand.isActive ? "success" : "neutral"}>
          {brand.isActive ? common("active") : common("inactive")}
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
          <Link href="/admin/product-brands/new" className="btn-primary !px-4 !py-2">
            <Plus className="h-4 w-4" />
            {t("new")}
          </Link>
        }
      />

      <AdminTable
        columns={columns}
        data={data.items}
        rowKey={(brand) => brand.id}
        emptyMessage={list("empty", { entity: t("entity") })}
        actionsLabel={list("actions")}
        actions={(brand) => (
          <div className="flex justify-end gap-2">
            <Link
              href={`/admin/product-brands/${brand.id}`}
              className="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"
            >
              {list("edit")}
            </Link>
            <DeleteButton
              endpoint={`/product-brands/${brand.id}`}
              title={list("deleteTitle", { entity: t("entity") })}
              message={list("deleteConfirm", { name: brand.name })}
            />
          </div>
        )}
      />

      <Pagination
        pageIndex={pageIndex}
        totalCount={data.totalCount}
        pageSize={PAGE_SIZE}
        basePath="/admin/product-brands"
      />
    </div>
  );
}

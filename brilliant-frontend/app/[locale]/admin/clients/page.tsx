import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { Plus } from "lucide-react";
import { adminFetchList } from "@/lib/adminApi";
import type { Client } from "@/types";
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
  const t = await getTranslations({ locale, namespace: "admin.sections.clients" });
  return { title: t("title") };
}

const PAGE_SIZE = 10;

interface ClientsAdminPageProps {
  searchParams: { page?: string };
}

export default async function ClientsAdminPage({ searchParams }: ClientsAdminPageProps) {
  const t = await getTranslations("admin.sections.clients");
  const common = await getTranslations("common");
  const list = await getTranslations("admin.list");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;

  const data = await adminFetchList<Client>(
    `/clients?pageIndex=${pageIndex}&pageSize=${PAGE_SIZE}&onlyActive=false`,
  ).catch(() => ({ items: [], totalCount: 0 }));

  const columns: Column<Client>[] = [
    {
      key: "name",
      header: t("headers.client"),
      cell: (client) => (
        <Link
          href={`/admin/clients/${client.id}`}
          className="font-semibold text-neutral transition-colors hover:text-secondary"
        >
          {client.name}
        </Link>
      ),
    },
    { key: "order", header: t("headers.order"), cell: (client) => client.order ?? "—" },
    {
      key: "status",
      header: t("headers.status"),
      cell: (client) => (
        <Badge variant={client.isActive ? "success" : "neutral"}>
          {client.isActive ? common("active") : common("inactive")}
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
          <Link href="/admin/clients/new" className="btn-primary !px-4 !py-2">
            <Plus className="h-4 w-4" />
            {t("new")}
          </Link>
        }
      />

      <AdminTable
        columns={columns}
        data={data.items}
        rowKey={(client) => client.id}
        emptyMessage={list("empty", { entity: t("entity") })}
        actionsLabel={list("actions")}
        actions={(client) => (
          <div className="flex justify-end gap-2">
            <Link
              href={`/admin/clients/${client.id}`}
              className="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"
            >
              {list("edit")}
            </Link>
            <DeleteButton
              endpoint={`/clients/${client.id}`}
              title={list("deleteTitle", { entity: t("entity") })}
              message={list("deleteConfirm", { name: client.name })}
            />
          </div>
        )}
      />

      <Pagination
        pageIndex={pageIndex}
        totalCount={data.totalCount}
        pageSize={PAGE_SIZE}
        basePath="/admin/clients"
      />
    </div>
  );
}

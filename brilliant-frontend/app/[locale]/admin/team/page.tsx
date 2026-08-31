import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { Plus } from "lucide-react";
import { adminFetchList } from "@/lib/adminApi";
import type { TeamMember } from "@/types";
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
  const t = await getTranslations({ locale, namespace: "admin.sections.team" });
  return { title: t("title") };
}

const PAGE_SIZE = 10;

interface TeamAdminPageProps {
  searchParams: { page?: string };
}

export default async function TeamAdminPage({ searchParams }: TeamAdminPageProps) {
  const t = await getTranslations("admin.sections.team");
  const common = await getTranslations("common");
  const list = await getTranslations("admin.list");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;

  const data = await adminFetchList<TeamMember>(
    `/team?pageIndex=${pageIndex}&pageSize=${PAGE_SIZE}&onlyActive=false`,
  ).catch(() => ({ items: [], totalCount: 0 }));

  const columns: Column<TeamMember>[] = [
    {
      key: "name",
      header: t("headers.member"),
      cell: (member) => (
        <div>
          <Link
            href={`/admin/team/${member.id}`}
            className="font-semibold text-neutral transition-colors hover:text-secondary"
          >
            {member.name}
          </Link>
          <p className="mt-0.5 text-xs text-neutral/50">{member.jobTitle || "—"}</p>
        </div>
      ),
    },
    { key: "order", header: t("headers.order"), cell: (member) => member.order ?? "—" },
    {
      key: "status",
      header: t("headers.status"),
      cell: (member) => (
        <Badge variant={member.isActive ? "success" : "neutral"}>
          {member.isActive ? common("active") : common("inactive")}
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
          <Link href="/admin/team/new" className="btn-primary !px-4 !py-2">
            <Plus className="h-4 w-4" />
            {t("new")}
          </Link>
        }
      />

      <AdminTable
        columns={columns}
        data={data.items}
        rowKey={(member) => member.id}
        emptyMessage={list("empty", { entity: t("entity") })}
        actionsLabel={list("actions")}
        actions={(member) => (
          <div className="flex justify-end gap-2">
            <Link
              href={`/admin/team/${member.id}`}
              className="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"
            >
              {list("edit")}
            </Link>
            <DeleteButton
              endpoint={`/team/${member.id}`}
              title={list("deleteTitle", { entity: t("entity") })}
              message={list("deleteConfirm", { name: member.name })}
            />
          </div>
        )}
      />

      <Pagination
        pageIndex={pageIndex}
        totalCount={data.totalCount}
        pageSize={PAGE_SIZE}
        basePath="/admin/team"
      />
    </div>
  );
}

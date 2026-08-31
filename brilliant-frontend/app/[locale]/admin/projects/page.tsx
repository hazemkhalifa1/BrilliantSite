import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { Plus } from "lucide-react";
import { adminFetchList } from "@/lib/adminApi";
import type { Project, ProjectType } from "@/types";
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
  const t = await getTranslations({ locale, namespace: "admin.sections.projects" });
  return { title: t("title") };
}

const PAGE_SIZE = 10;

interface ProjectsAdminPageProps {
  searchParams: { page?: string; typeId?: string };
}

export default async function ProjectsAdminPage({ searchParams }: ProjectsAdminPageProps) {
  const t = await getTranslations("admin.sections.projects");
  const common = await getTranslations("common");
  const list = await getTranslations("admin.list");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;
  const typeId =
    searchParams.typeId && !Number.isNaN(Number(searchParams.typeId)) ? Number(searchParams.typeId) : null;

  const query = new URLSearchParams({
    pageIndex: String(pageIndex),
    pageSize: String(PAGE_SIZE),
    onlyActive: "false",
  });
  if (typeId) query.set("typeId", String(typeId));

  const [data, types] = await Promise.all([
    adminFetchList<Project>(`/projects?${query.toString()}`).catch(() => ({ items: [], totalCount: 0 })),
    adminFetchList<ProjectType>("/project-types?pageSize=100").catch(() => ({ items: [], totalCount: 0 })),
  ]);

  const filterUrl = (selected: number | null, page = 1) => {
    const params = new URLSearchParams();
    if (selected !== null) params.set("typeId", String(selected));
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `/admin/projects?${qs}` : "/admin/projects";
  };

  const columns: Column<Project>[] = [
    {
      key: "title",
      header: t("headers.project"),
      cell: (project) => (
        <div>
          <Link
            href={`/admin/projects/${project.id}`}
            className="font-semibold text-neutral transition-colors hover:text-secondary"
          >
            {project.title}
          </Link>
          <p className="mt-0.5 text-xs text-neutral/50">{truncate(project.description, 70)}</p>
        </div>
      ),
    },
    { key: "type", header: t("headers.type"), cell: (project) => project.typeName || "—" },
    { key: "year", header: t("headers.year"), cell: (project) => project.year || "—" },
    { key: "order", header: t("headers.order"), cell: (project) => project.order ?? "—" },
    {
      key: "status",
      header: t("headers.status"),
      cell: (project) => (
        <Badge variant={project.isActive ? "success" : "neutral"}>
          {project.isActive ? common("active") : common("inactive")}
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
          <Link href="/admin/projects/new" className="btn-primary !px-4 !py-2">
            <Plus className="h-4 w-4" />
            {t("new")}
          </Link>
        }
      />

      <div className="flex flex-wrap items-center gap-2">
        <Link
          href="/admin/projects"
          className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
            !typeId ? "border-primary bg-primary text-white" : "border-line text-neutral hover:bg-neutral-light"
          }`}
        >
          {list("all")}
        </Link>
        {types.items.map((type) => (
          <Link
            key={type.id}
            href={filterUrl(type.id)}
            className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
              typeId === type.id
                ? "border-primary bg-primary text-white"
                : "border-line text-neutral hover:bg-neutral-light"
            }`}
          >
            {type.name}
          </Link>
        ))}
      </div>

      <AdminTable
        columns={columns}
        data={data.items}
        rowKey={(project) => project.id}
        emptyMessage={list("empty", { entity: t("entity") })}
        actionsLabel={list("actions")}
        actions={(project) => (
          <div className="flex justify-end gap-2">
            <Link
              href={`/admin/projects/${project.id}`}
              className="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"
            >
              {list("edit")}
            </Link>
            <DeleteButton
              endpoint={`/projects/${project.id}`}
              title={list("deleteTitle", { entity: t("entity") })}
              message={list("deleteConfirm", { name: project.title })}
            />
          </div>
        )}
      />

      <Pagination
        pageIndex={pageIndex}
        totalCount={data.totalCount}
        pageSize={PAGE_SIZE}
        basePath="/admin/projects"
        queryParams={{ typeId: typeId ?? undefined }}
      />
    </div>
  );
}

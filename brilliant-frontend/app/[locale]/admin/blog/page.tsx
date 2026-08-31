import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { Plus } from "lucide-react";
import { adminFetchList } from "@/lib/adminApi";
import type { BlogPost } from "@/types";
import { formatDate } from "@/lib/utils";
import { AdminPageHeader } from "@/components/admin/AdminPageHeader";
import { AdminTable, type Column } from "@/components/admin/AdminTable";
import { DeleteButton } from "@/components/admin/DeleteButton";
import { PublishToggleButton } from "@/components/admin/PublishToggleButton";
import { Badge } from "@/components/ui/Badge";
import { Pagination } from "@/components/ui/Pagination";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "admin.sections.blog" });
  return { title: t("title") };
}

const PAGE_SIZE = 10;

interface BlogAdminPageProps {
  searchParams: { page?: string; status?: string };
}

export default async function BlogAdminPage({ searchParams }: BlogAdminPageProps) {
  const t = await getTranslations("admin.sections.blog");
  const list = await getTranslations("admin.list");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;
  const status = searchParams.status === "published" ? "published" : searchParams.status === "draft" ? "draft" : null;

  const query = new URLSearchParams({ pageIndex: String(pageIndex), pageSize: String(PAGE_SIZE) });
  if (status === "published") query.set("publishedOnly", "true");

  const data = await adminFetchList<BlogPost>(`/blog?${query.toString()}`).catch(() => ({
    items: [],
    totalCount: 0,
  }));

  const filterUrl = (selected: string | null, page = 1) => {
    const params = new URLSearchParams();
    if (selected) params.set("status", selected);
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `/admin/blog?${qs}` : "/admin/blog";
  };

  const columns: Column<BlogPost>[] = [
    {
      key: "title",
      header: t("headers.post"),
      cell: (post) => (
        <div>
          <Link
            href={`/admin/blog/${post.id}`}
            className="font-semibold text-neutral transition-colors hover:text-secondary"
          >
            {post.title}
          </Link>
          <p className="mt-0.5 text-xs text-neutral/50">/{post.slug}</p>
        </div>
      ),
    },
    {
      key: "tags",
      header: t("headers.tags"),
      cell: (post) =>
        post.tags && post.tags.length > 0 ? post.tags.map((tag) => tag.name).join(", ") : "—",
    },
    {
      key: "status",
      header: t("headers.status"),
      cell: (post) =>
        post.isPublished ? <Badge variant="success">{list("published")}</Badge> : <Badge variant="neutral">{list("drafts")}</Badge>,
    },
    { key: "order", header: t("headers.order"), cell: (post) => post.order ?? "—" },
    { key: "date", header: t("headers.published"), cell: (post) => formatDate(post.publishedAt) },
  ];

  return (
    <div className="space-y-6">
      <AdminPageHeader
        title={t("title")}
        description={t("description")}
        action={
          <Link href="/admin/blog/new" className="btn-primary !px-4 !py-2">
            <Plus className="h-4 w-4" />
            {t("new")}
          </Link>
        }
      />

      <div className="flex flex-wrap items-center gap-2">
        <Link
          href="/admin/blog"
          className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
            !status ? "border-primary bg-primary text-white" : "border-line text-neutral hover:bg-neutral-light"
          }`}
        >
          {list("all")}
        </Link>
        <Link
          href={filterUrl("published")}
          className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
            status === "published"
              ? "border-primary bg-primary text-white"
              : "border-line text-neutral hover:bg-neutral-light"
          }`}
        >
          {list("published")}
        </Link>
        <Link
          href={filterUrl("draft")}
          className={`border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors ${
            status === "draft"
              ? "border-primary bg-primary text-white"
              : "border-line text-neutral hover:bg-neutral-light"
          }`}
        >
          {list("drafts")}
        </Link>
      </div>

      <AdminTable
        columns={columns}
        data={data.items}
        rowKey={(post) => post.id}
        emptyMessage={list("empty", { entity: t("entity") })}
        actionsLabel={list("actions")}
        actions={(post) => (
          <div className="flex items-center justify-end gap-2">
            <Link
              href={`/admin/blog/${post.id}`}
              className="inline-flex h-8 items-center border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary"
            >
              {list("edit")}
            </Link>
            <PublishToggleButton id={post.id} isPublished={post.isPublished} />
            <DeleteButton
              endpoint={`/blog/${post.id}`}
              title={list("deleteTitle", { entity: t("entity") })}
              message={list("deleteConfirm", { name: post.title })}
            />
          </div>
        )}
      />

      <Pagination
        pageIndex={pageIndex}
        totalCount={data.totalCount}
        pageSize={PAGE_SIZE}
        basePath="/admin/blog"
        queryParams={{ status: status ?? undefined }}
      />
    </div>
  );
}

import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { apiFetch } from "@/lib/api";
import type { BlogPost, PagedResult, Tag } from "@/types";
import { cn } from "@/lib/utils";
import { PageHeader } from "@/components/public/PageHeader";
import { BlogCard } from "@/components/public/BlogCard";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "blog" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
  };
}

const PAGE_SIZE = 9;

async function getTags() {
  return apiFetch<PagedResult<Tag>>("/tags?pageSize=50")
    .then((data) => data.items)
    .catch(() => [] as Tag[]);
}

interface BlogPageProps {
  searchParams: { tagId?: string; page?: string };
}

export default async function BlogPage({ searchParams }: BlogPageProps) {
  const t = await getTranslations("blog");
  const tags = await getTags();

  const tagId =
    searchParams.tagId && !Number.isNaN(Number(searchParams.tagId)) ? Number(searchParams.tagId) : null;
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Math.max(1, Number(searchParams.page)) : 1;

  const query = new URLSearchParams({
    publishedOnly: "true",
    pageSize: String(PAGE_SIZE),
    pageIndex: String(pageIndex),
  });
  if (tagId) query.set("tagId", String(tagId));

  const result = await apiFetch<PagedResult<BlogPost>>(`/blog?${query.toString()}`)
    .catch(() => ({ items: [] as BlogPost[], totalCount: 0, pageIndex: 1, pageSize: PAGE_SIZE }));

  const totalPages = Math.max(1, Math.ceil(result.totalCount / PAGE_SIZE));
  const makeUrl = (tag: number | null, page: number) => {
    const params = new URLSearchParams();
    if (tag !== null) params.set("tagId", String(tag));
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `/blog?${qs}` : "/blog";
  };

  return (
    <div>
      <PageHeader
        eyebrow={t("eyebrow")}
        title={t("title")}
        subtitle={t("subtitle")}
        backgroundImage="https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1920&q=80"
      />

      <section className="py-16 md:py-20">
        <div className="container-brilliant">
          {tags.length > 0 && (
            <div className="flex flex-wrap gap-2">
              <Link
                href="/blog"
                className={cn(
                  "border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors",
                  !tagId ? "border-primary bg-primary text-white" : "border-line text-neutral hover:bg-neutral-light",
                )}
              >
                {t("allPosts")}
              </Link>
              {tags.map((tag) => (
                <Link
                  key={tag.id}
                  href={makeUrl(tag.id, 1)}
                  className={cn(
                    "border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors",
                    tagId === tag.id
                      ? "border-primary bg-primary text-white"
                      : "border-line text-neutral hover:bg-neutral-light",
                  )}
                >
                  {tag.name}
                </Link>
              ))}
            </div>
          )}

          {result.items.length > 0 ? (
            <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {result.items.map((post) => (
                <BlogCard key={post.id} post={post} />
              ))}
            </div>
          ) : (
            <p className="mt-12 text-center text-neutral/60">{t("noPosts")}</p>
          )}

          {totalPages > 1 && (
            <div className="mt-12 flex items-center justify-center gap-2">
              <Link
                href={makeUrl(tagId, pageIndex - 1)}
                className={cn(
                  "border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                  pageIndex <= 1 && "pointer-events-none opacity-40",
                )}
              >
                {t("previous")}
              </Link>
              <span className="px-3 text-sm text-neutral/60">
                {t("pageOf", { current: pageIndex, total: totalPages })}
              </span>
              <Link
                href={makeUrl(tagId, pageIndex + 1)}
                className={cn(
                  "border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                  pageIndex >= totalPages && "pointer-events-none opacity-40",
                )}
              >
                {t("next")}
              </Link>
            </div>
          )}
        </div>
      </section>
    </div>
  );
}

import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { SITE_URL, localeAlternates } from "@/lib/seo";
import { apiFetch } from "@/lib/api";
import type { BlogPost, PagedResult, Tag } from "@/types";
import { cn } from "@/lib/utils";
import { BlogCard } from "@/components/public/BlogCard";
import { Reveal } from "@/components/public/Reveal";
import { Parallax } from "@/components/public/Parallax";
import { ArrowLeft, ArrowRight } from "lucide-react";

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
    alternates: localeAlternates(locale, "/blog"),
  };
}

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";

const heroGridStyle = {
  backgroundImage:
    "linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px)",
  backgroundSize: "32px 32px",
} as const;

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
  const nav = await getTranslations("nav");
  const locale = await getLocale();
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
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            itemListElement: [
              {
                "@type": "ListItem",
                position: 1,
                name: nav("home"),
                item: `${SITE_URL}/${locale}`,
              },
              {
                "@type": "ListItem",
                position: 2,
                name: t("title"),
                item: `${SITE_URL}/${locale}/blog`,
              },
            ],
          }),
        }}
      />

      <section className="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
        <div className="absolute inset-0" style={heroGridStyle} />
        <div className="absolute inset-0">
          <Parallax speed={0.08} className="h-full w-full">
            <div
              className="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity"
              style={{ backgroundImage: `url('${HERO_IMAGE}')` }}
            />
          </Parallax>
        </div>
        <div className="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50" />

        <div className="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
          <span className="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white">
            {t("eyebrow")}
          </span>
          <h1 className="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl">
            {t("title")}
          </h1>
          <p className="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl">
            {t("subtitle")}
          </p>
        </div>
      </section>

      <section className="py-16 md:py-20">
        <div className="container-brilliant">
          {tags.length > 0 && (
            <Reveal>
              <div className="flex flex-wrap gap-2">
                <Link
                  href="/blog"
                  className={cn(
                    "border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-transform hover:-translate-y-0.5",
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
                      "border px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-transform hover:-translate-y-0.5",
                      tagId === tag.id
                        ? "border-primary bg-primary text-white"
                        : "border-line text-neutral hover:bg-neutral-light",
                    )}
                  >
                    {tag.name}
                  </Link>
                ))}
              </div>
            </Reveal>
          )}

          {result.items.length > 0 ? (
            <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {result.items.map((post, index) => (
                <Reveal key={post.id} delay={index * 80} from={index % 3 === 0 ? "left" : index % 3 === 2 ? "right" : "up"}>
                  <div className="pop-card h-full" style={{ transitionDelay: `${index * 80 + 120}ms` }}>
                    <BlogCard post={post} />
                  </div>
                </Reveal>
              ))}
            </div>
          ) : (
            <p className="mt-12 text-center text-neutral/60">{t("noPosts")}</p>
          )}

          {totalPages > 1 && (
            <Reveal delay={120}>
              <div className="mt-12 flex items-center justify-center gap-2">
                <Link
                  href={makeUrl(tagId, pageIndex - 1)}
                  className={cn(
                    "group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                    pageIndex <= 1 && "pointer-events-none opacity-40",
                  )}
                >
                  <ArrowLeft className="mr-2 inline h-4 w-4 transition-transform duration-300 group-hover:-translate-x-1" />
                  {t("previous")}
                </Link>
                <span className="px-3 text-sm text-neutral/60">
                  {t("pageOf", { current: pageIndex, total: totalPages })}
                </span>
                <Link
                  href={makeUrl(tagId, pageIndex + 1)}
                  className={cn(
                    "group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                    pageIndex >= totalPages && "pointer-events-none opacity-40",
                  )}
                >
                  {t("next")}
                  <ArrowRight className="ml-2 inline h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
                </Link>
              </div>
            </Reveal>
          )}
        </div>
      </section>
    </div>
  );
}

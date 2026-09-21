import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { localized } from "@/lib/localize";
import { SITE_URL, localeAlternates } from "@/lib/seo";
import { notFound } from "next/navigation";
import { ArrowLeft, CalendarDays, Clock } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { BlogPost, PagedResult } from "@/types";
import { formatDate, getImageUrl } from "@/lib/utils";
import { cleanArticleHtml, readingMinutes } from "@/lib/article";
import { BlogCard } from "@/components/public/BlogCard";
import { SiteImage } from "@/components/ui/SiteImage";
import { Reveal } from "@/components/public/Reveal";
import { Parallax } from "@/components/public/Parallax";
import { ReadingProgress } from "@/components/public/ReadingProgress";

export const dynamic = "force-dynamic";

const PAGE_URL = (locale: string) => `${SITE_URL}/${locale}`;

interface BlogPostPageProps {
  params: { slug: string; locale: string };
}

async function getPost(slug: string): Promise<BlogPost | null> {
  return apiFetch<BlogPost>(`/blog/${slug}`).catch(() => null);
}

async function getRelated(post: BlogPost): Promise<BlogPost[]> {
  const tagId = post.tags?.[0]?.id;
  if (tagId) {
    const byTag = await apiFetch<PagedResult<BlogPost>>(
      `/blog?publishedOnly=true&tagId=${tagId}&pageSize=3`,
    ).catch(() => null);
    if (byTag) {
      const items = byTag.items.filter((item) => item.slug !== post.slug);
      if (items.length > 0) return items;
    }
  }

  const latest = await apiFetch<PagedResult<BlogPost>>("/blog?publishedOnly=true&pageSize=3").catch(
    () => null,
  );
  return (latest?.items ?? []).filter((item) => item.slug !== post.slug);
}

function ogImage(post: BlogPost): string {
  const cover = getImageUrl(post.coverImagePath);
  if (cover && cover !== "/placeholder.svg") return cover.startsWith("/") ? `${SITE_URL}${cover}` : cover;
  return `${SITE_URL}/logo.png`;
}

export async function generateMetadata({ params }: BlogPostPageProps): Promise<Metadata> {
  const t = await getTranslations({ locale: params.locale, namespace: "blogPost" });
  const post = await getPost(params.slug);
  if (!post) return { title: t("notFound") };
  const title = localized(params.locale, post.metaTitle || post.title, post.metaTitleAr || post.titleAr);
  const description = localized(params.locale, post.metaDescription, post.metaDescriptionAr);
  return {
    title,
    description,
    alternates: localeAlternates(params.locale, `/blog/${post.slug}`),
    openGraph: {
      title,
      description,
      url: `${PAGE_URL(params.locale)}/blog/${post.slug}`,
      siteName: "Brilliant Engineering",
      locale: params.locale === "ar" ? "ar_EG" : "en_US",
      images: [
        {
          url: ogImage(post),
          width: 1200,
          height: 630,
          alt: title,
        },
      ],
      type: "article",
      publishedTime: post.publishedAt || undefined,
    },
    twitter: {
      card: "summary_large_image",
      title,
      description,
      images: [ogImage(post)],
    },
  };
}

export default async function BlogPostPage({ params }: BlogPostPageProps) {
  const t = await getTranslations("blogPost");
  const nav = await getTranslations("nav");
  const locale = await getLocale();
  const post = await getPost(params.slug);
  if (!post) notFound();

  const title = localized(locale, post.title, post.titleAr);
  const rawContent = localized(locale, post.content, post.contentAr);
  const bodyHtml = cleanArticleHtml(rawContent, title);
  const minutes = readingMinutes(rawContent);
  const related = await getRelated(post);
  const postUrl = `${PAGE_URL(locale)}/blog/${post.slug}`;

  return (
    <article className="relative">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Article",
            headline: title,
            description: localized(locale, post.metaDescription, post.metaDescriptionAr) || undefined,
            image: ogImage(post),
            datePublished: post.publishedAt || undefined,
            dateModified: post.publishedAt || post.createdAt || undefined,
            inLanguage: locale === "ar" ? "ar-EG" : "en",
            url: postUrl,
            mainEntityOfPage: {
              "@type": "WebPage",
              "@id": postUrl,
            },
            author: {
              "@type": "Organization",
              name: "Brilliant Engineering Co.",
            },
            publisher: {
              "@type": "Organization",
              name: "Brilliant Engineering Co.",
              logo: {
                "@type": "ImageObject",
                url: `${SITE_URL}/logo.png`,
              },
            },
          }),
        }}
      />

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
                item: PAGE_URL(locale),
              },
              {
                "@type": "ListItem",
                position: 2,
                name: nav("blog"),
                item: `${PAGE_URL(locale)}/blog`,
              },
              {
                "@type": "ListItem",
                position: 3,
                name: title,
                item: postUrl,
              },
            ],
          }),
        }}
      />

      <ReadingProgress />

      <header className="relative flex min-h-[64vh] items-center overflow-hidden bg-neutral md:min-h-[72vh]">
        <div className="absolute inset-0">
          <Parallax speed={0.1} className="h-full w-full">
            <SiteImage src={post.coverImagePath} alt={title} className="h-full w-full" eager />
          </Parallax>
        </div>
        <div className="absolute inset-0 bg-gradient-to-b from-neutral/80 via-neutral/60 to-neutral/90" />

        <div className="container-brilliant relative z-10 w-full py-32 md:py-40">
          <div className="mx-auto max-w-3xl text-center">
            <Reveal>
              <Link
                href="/blog"
                className="inline-flex items-center gap-2 font-headline text-xs font-semibold uppercase tracking-[0.25em] text-white/70 transition-colors hover:text-white"
              >
                <ArrowLeft className="h-4 w-4 rtl:rotate-180" />
                {t("allPosts")}
              </Link>
            </Reveal>

            <Reveal delay={80}>
              <div className="mt-8 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm text-white/80">
                <span className="inline-flex items-center gap-1.5">
                  <CalendarDays className="h-4 w-4" />
                  {formatDate(post.publishedAt, locale)}
                </span>
                {minutes > 0 && (
                  <>
                    <span className="h-1 w-1 rounded-full bg-white/40" aria-hidden="true" />
                    <span className="inline-flex items-center gap-1.5">
                      <Clock className="h-4 w-4" />
                      {t("readingTime", { count: minutes })}
                    </span>
                  </>
                )}
              </div>
            </Reveal>

            <Reveal delay={140}>
              <h1 className="mt-7 font-headline text-4xl font-bold uppercase leading-[1.05] tracking-tighter text-white md:text-5xl lg:text-6xl">
                {title}
              </h1>
              <span className="mx-auto mt-8 block h-1 w-24 bg-tertiary" aria-hidden="true" />
            </Reveal>

            {post.tags && post.tags.length > 0 && (
              <Reveal delay={200}>
                <div className="mt-8 flex flex-wrap items-center justify-center gap-2">
                  {post.tags.map((tag) => (
                    <span
                      key={tag.id}
                      className="border border-white/25 px-3 py-1 font-headline text-xs font-semibold uppercase tracking-widest text-white/90"
                    >
                      {tag.name}
                    </span>
                  ))}
                </div>
              </Reveal>
            )}
          </div>
        </div>
      </header>

      <div className="container-brilliant py-16 md:py-24">
        <div className="mx-auto max-w-3xl">
          <div className="article-body" dangerouslySetInnerHTML={{ __html: bodyHtml }} />
        </div>
      </div>

      {related.length > 0 && (
        <section className="border-t border-line bg-neutral-light py-16 md:py-20">
          <div className="container-brilliant">
            <Reveal>
              <h2 className="font-headline text-2xl font-bold uppercase tracking-tight">
                {t("relatedPosts")}
              </h2>
              <span className="mt-3 block h-[3px] w-12 bg-tertiary" aria-hidden="true" />
            </Reveal>
            <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {related.map((item, index) => (
                <Reveal key={item.id} delay={index * 90} from={index % 2 === 0 ? "left" : "right"}>
                  <div className="pop-card h-full" style={{ transitionDelay: `${index * 90 + 100}ms` }}>
                    <BlogCard post={item} />
                  </div>
                </Reveal>
              ))}
            </div>
          </div>
        </section>
      )}
    </article>
  );
}
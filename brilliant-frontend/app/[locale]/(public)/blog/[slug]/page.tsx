import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { localized } from "@/lib/localize";
import { notFound } from "next/navigation";
import { CalendarDays, ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { BlogPost, PagedResult } from "@/types";
import { formatDate, getImageUrl } from "@/lib/utils";
import { Badge } from "@/components/ui/Badge";
import { BlogCard } from "@/components/public/BlogCard";
import { SiteImage } from "@/components/ui/SiteImage";

export const dynamic = "force-dynamic";

const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL || "https://brilliant-eng.com";

interface BlogPostPageProps {
  params: { slug: string; locale: string };
}

async function getPost(slug: string): Promise<BlogPost | null> {
  return apiFetch<BlogPost>(`/blog/${slug}`).catch(() => null);
}

export async function generateMetadata({ params }: BlogPostPageProps): Promise<Metadata> {
  const t = await getTranslations({ locale: params.locale, namespace: "blogPost" });
  const post = await getPost(params.slug);
  if (!post) return { title: t("notFound") };
  return {
    title: localized(params.locale, post.metaTitle || post.title, post.metaTitleAr || post.titleAr),
    description: localized(params.locale, post.metaDescription, post.metaDescriptionAr),
    openGraph: {
      title: localized(params.locale, post.metaTitle || post.title, post.metaTitleAr || post.titleAr),
      description: localized(params.locale, post.metaDescription, post.metaDescriptionAr),
      images: [getImageUrl(post.coverImagePath)],
      type: "article",
      publishedTime: post.publishedAt || undefined,
    },
  };
}

export default async function BlogPostPage({ params }: BlogPostPageProps) {
  const t = await getTranslations("blogPost");
  const locale = await getLocale();
  const post = await getPost(params.slug);
  if (!post) notFound();

  const firstTagId = post.tags?.[0]?.id;
  let related: BlogPost[] = [];
  if (firstTagId) {
    related = await apiFetch<PagedResult<BlogPost>>(
      `/blog?publishedOnly=true&tagId=${firstTagId}&pageSize=3`,
    )
      .then((data) => data.items.filter((item) => item.slug !== post.slug))
      .catch(() => [] as BlogPost[]);
  }

  return (
    <article>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Article",
            headline: localized(locale, post.title, post.titleAr),
            description: localized(locale, post.metaDescription, post.metaDescriptionAr) || undefined,
            image: getImageUrl(post.coverImagePath),
            datePublished: post.publishedAt || undefined,
            url: `${SITE_URL}/blog/${post.slug}`,
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
      <div className="relative aspect-[21/9] overflow-hidden bg-neutral-light">
        <SiteImage src={post.coverImagePath} alt={localized(locale, post.title, post.titleAr)} className="h-full w-full" eager />
      </div>

      <div className="container-brilliant py-12 md:py-16">
        <div className="mx-auto max-w-3xl">
          <Link
            href="/blog"
            className="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-secondary transition-colors hover:text-tertiary"
          >
            <ArrowLeft className="h-4 w-4" />
            {t("allPosts")}
          </Link>

          <div className="mt-6 flex flex-wrap items-center gap-3">
            <span className="inline-flex items-center gap-1.5 text-sm text-neutral/60">
              <CalendarDays className="h-4 w-4" />
              {formatDate(post.publishedAt)}
            </span>
            {post.tags?.map((tag) => (
              <Badge key={tag.id} variant="outline">
                {tag.name}
              </Badge>
            ))}
          </div>

          <h1 className="mt-4 font-headline text-3xl font-bold uppercase leading-tight md:text-4xl">
            {localized(locale, post.title, post.titleAr)}
          </h1>
          <span className="mt-4 block h-[3px] w-16 bg-tertiary" aria-hidden="true" />

          <div
            className="prose-none mt-8 space-y-5 leading-relaxed text-neutral/80 [&_h1]:font-headline [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:uppercase [&_h2]:font-headline [&_h2]:text-xl [&_h2]:font-bold [&_h2]:uppercase [&_h3]:font-headline [&_h3]:font-bold [&_h3]:uppercase [&_a]:text-secondary [&_a]:underline [&_img]:my-6 [&_img]:w-full [&_blockquote]:border-s-4 [&_blockquote]:border-tertiary [&_blockquote]:ps-4 [&_li]:ms-6 [&_li]:list-disc [&_ul]:space-y-2 [&_ol]:list-decimal [&_ol]:space-y-2 [&_li]:ml-6"
            dangerouslySetInnerHTML={{ __html: localized(locale, post.content, post.contentAr) }}
          />
        </div>
      </div>

      {related.length > 0 && (
        <section className="bg-neutral-light py-16">
          <div className="container-brilliant">
            <h2 className="font-headline text-xl font-bold uppercase">{t("relatedPosts")}</h2>
            <span className="mt-2 block h-[3px] w-10 bg-tertiary" aria-hidden="true" />
            <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {related.map((item) => (
                <BlogCard key={item.id} post={item} />
              ))}
            </div>
          </div>
        </section>
      )}
    </article>
  );
}

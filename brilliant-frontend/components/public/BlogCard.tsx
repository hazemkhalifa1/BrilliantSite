import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { localized } from "@/lib/localize";
import { ArrowRight, CalendarDays } from "lucide-react";
import type { BlogPost } from "@/types";
import { formatDate, truncate } from "@/lib/utils";
import { Badge } from "@/components/ui/Badge";
import { SiteImage } from "@/components/ui/SiteImage";

export async function BlogCard({ post }: { post: BlogPost }) {
  const t = await getTranslations("common");
  const locale = await getLocale();
  return (
    <article className="card-brilliant group flex flex-col overflow-hidden transition-colors hover:border-tertiary">
      <Link
        href={`/blog/${post.slug}`}
        className="relative block aspect-[16/9] overflow-hidden bg-neutral-light"
      >
        <SiteImage
          src={post.coverImagePath}
          alt={localized(locale, post.title, post.titleAr)}
          className="h-full w-full transition-transform duration-300 group-hover:scale-105"
        />
      </Link>
      <div className="flex flex-1 flex-col p-5">
        <div className="flex items-center gap-3">
          <span className="inline-flex items-center gap-1.5 text-xs text-neutral/60">
            <CalendarDays className="h-3.5 w-3.5" />
            {formatDate(post.publishedAt)}
          </span>
          {post.tags && post.tags.length > 0 && (
            <Badge variant="outline">{post.tags[0].name}</Badge>
          )}
        </div>
        <Link
          href={`/blog/${post.slug}`}
          className="mt-3 font-headline text-lg font-bold uppercase leading-snug transition-colors group-hover:text-secondary"
        >
          {localized(locale, post.title, post.titleAr)}
        </Link>
        <p className="mt-2 flex-1 text-sm leading-relaxed text-neutral/70">
          {truncate(
            localized(locale, post.metaDescription || post.content.replace(/<[^>]*>/g, ""), post.metaDescriptionAr || post.contentAr),
            120,
          )}
        </p>
        <Link
          href={`/blog/${post.slug}`}
          className="mt-4 inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-secondary transition-colors hover:text-tertiary"
        >
          {t("readMore")}
          <ArrowRight className="h-4 w-4" />
        </Link>
      </div>
    </article>
  );
}

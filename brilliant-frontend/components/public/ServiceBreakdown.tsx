"use client";

import * as React from "react";
import { Link } from "@/src/i18n/navigation";
import { useLocale, useTranslations } from "next-intl";
import { ArrowRight, ChevronDown } from "lucide-react";
import type { Service, ServiceCategory } from "@/types";
import { cn } from "@/lib/utils";
import { localized } from "@/lib/localize";
import { Reveal } from "@/components/public/Reveal";
import { SiteImage } from "@/components/ui/SiteImage";

const FALLBACK_IMAGE =
  "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";

const INITIAL_VISIBLE = 3;
const STEP = 3;

interface ServiceBreakdownProps {
  services: Service[];
  categories?: ServiceCategory[];
  activeCategoryId?: number | null;
}

const chipBase =
  "border-2 px-5 py-2 font-headline text-sm font-bold uppercase tracking-widest transition-colors";

const chipActive = "border-[#111C2D] bg-[#0059BB] text-white";

const chipIdle = "border-[#111C2D] text-[#111C2D] hover:bg-[#BA1A1A] hover:text-white";

export function ServiceBreakdown({
  services,
  categories = [],
  activeCategoryId = null,
}: ServiceBreakdownProps) {
  const t = useTranslations("serviceBreakdown");
  const locale = useLocale();
  const [visibleCount, setVisibleCount] = React.useState(INITIAL_VISIBLE);

  const visible = services.slice(0, visibleCount);
  const hasMore = visibleCount < services.length;
  const activeCategory = categories.find((category) => category.id === activeCategoryId) ?? null;

  return (
    <section id="core" className="bg-white py-24 md:py-32">
      <div className="container-brilliant">
        <div className="mb-16 text-center">
          <Reveal>
            <div>
              <h2 className="font-headline text-4xl font-bold uppercase tracking-tighter text-[#111C2D] md:text-5xl">
                {t("title1")} <span className="italic text-[#BA1A1A]">{t("titleAccent")}</span>
              </h2>
              <div className="mx-auto mt-6 h-1 w-24 bg-[#0059BB]" />
            </div>
          </Reveal>
          <Reveal delay={140}>
            <p className="mx-auto mt-6 max-w-2xl text-lg text-[#4C4546]">
              {t("subtitle")}
            </p>
          </Reveal>
        </div>

        {categories.length > 0 && (
          <Reveal delay={200}>
            <div className="mb-16 flex flex-wrap justify-center gap-2">
              <Link href="/services" className={cn(chipBase, !activeCategoryId ? chipActive : chipIdle)}>
                {t("allServices")}
              </Link>
              {categories.map((category) => (
                <Link
                  key={category.id}
                  href={`/services?categoryId=${category.id}`}
                  className={cn(chipBase, activeCategoryId === category.id ? chipActive : chipIdle)}
                >
                  {localized(locale, category.name, category.nameAr)}
                </Link>
              ))}
            </div>
          </Reveal>
        )}

        {activeCategory && activeCategory.description && (
          <Reveal>
            <div className="pop-card mx-auto mb-16 max-w-2xl border-l-4 border-[#BA1A1A] bg-white p-6 shadow-[4px_4px_0px_0px_#111C2D]">
              <p className="font-headline text-sm font-bold uppercase tracking-widest text-[#BA1A1A]">
                {localized(locale, activeCategory.name, activeCategory.nameAr)}
              </p>
              <p className="mt-2 text-lg leading-relaxed text-[#4C4546]">
                {localized(locale, activeCategory.description, activeCategory.descriptionAr)}
              </p>
            </div>
          </Reveal>
        )}

        <div className="space-y-24 md:space-y-32">
          {visible.map((service, index) => (
            <div
              key={service.id}
              className={cn(
                "flex flex-col items-center gap-10 md:flex-row md:gap-16",
                index % 2 === 1 && "md:flex-row-reverse",
              )}
            >
              <Reveal className="w-full md:w-1/2" from={index % 2 === 0 ? "left" : "right"}>
                <div className="group relative overflow-hidden border-2 border-[#111C2D] bg-[#111C2D] shadow-[6px_6px_0px_0px_#BA1A1A]">
                  {service.iconPath ? (
                    <div className="h-72 w-full overflow-hidden md:h-96">
                      <SiteImage
                        src={service.iconPath}
                        alt={localized(locale, service.title, service.titleAr)}
                        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        eager
                      />
                    </div>
                  ) : (
                    <div
                      className="h-72 w-full bg-cover bg-center opacity-40 mix-blend-luminosity transition-transform duration-700 group-hover:scale-105 md:h-96"
                      style={{ backgroundImage: `url('${FALLBACK_IMAGE}')` }}
                    />
                  )}
                  <div className="absolute bottom-4 left-4 flex items-center gap-2 border-2 border-[#111C2D] bg-[#111C2D] px-3 py-1.5 font-mono text-xs font-bold uppercase tracking-widest text-white">
                    <span className="inline-block h-2 w-2 bg-[#BA1A1A]" />
                    SYS_ACTIVE // SRV_{String(index + 1).padStart(2, "0")}
                  </div>
                </div>
              </Reveal>

              <Reveal delay={100} className="w-full md:w-1/2" from={index % 2 === 0 ? "right" : "left"}>
                <span className="mb-4 inline-block border-2 border-[#111C2D] bg-[#0059BB] px-3 py-1 font-headline text-xs font-bold uppercase tracking-widest text-white">
                  {t("sector", { number: String(index + 1).padStart(2, "0") })}
                </span>
                {service.categoryName && (
                  <span className="mb-4 ml-2 inline-block border-2 border-[#111C2D] bg-[#BA1A1A] px-3 py-1 font-headline text-xs font-bold uppercase tracking-widest text-white">
                    {service.categoryName}
                  </span>
                )}
                <h2 className="font-headline text-4xl font-bold uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl">
                  {localized(locale, service.title, service.titleAr)}
                </h2>
                <p className="mt-6 text-lg leading-relaxed text-[#4C4546]">
                  {localized(locale, service.description, service.descriptionAr)}
                </p>
                <Link
                  href="/contact"
                  className="group mt-8 inline-flex items-center gap-2 bg-[#BA1A1A] px-8 py-3.5 font-headline text-sm font-bold uppercase tracking-widest text-white transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_#111C2D]"
                >
                  {t("requestService")}{" "}
                  <ArrowRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1.5" />
                </Link>
                {service.relatedBlogPostId && service.relatedBlogPostSlug && (
                  <Link
                    href={`/blog/${service.relatedBlogPostSlug}`}
                    className="mt-6 inline-block border-s-4 border-[#0059BB] bg-white px-5 py-3 text-sm font-semibold text-[#111C2D] shadow-[3px_3px_0px_0px_#0059BB] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[5px_5px_0px_0px_#BA1A1A]"
                  >
                    {t("relatedGuide")}{" "}
                    <span className="font-bold text-[#BA1A1A]">
                      {localized(
                        locale,
                        service.relatedBlogPostTitle ?? "",
                        service.relatedBlogPostTitleAr,
                      )}
                    </span>
                  </Link>
                )}
              </Reveal>
            </div>
          ))}
        </div>

        {hasMore && (
          <div className="mt-20 text-center">
            <button
              type="button"
              onClick={() =>
                setVisibleCount((count) => Math.min(count + STEP, services.length))
              }
              className="group inline-flex items-center gap-2 border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[4px_4px_0px_0px_#0059BB] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#0059BB]"
            >
              {t("showMore")}{" "}
              <ChevronDown className="h-5 w-5 transition-transform duration-300 group-hover:translate-y-1" />
            </button>
          </div>
        )}
      </div>
    </section>
  );
}

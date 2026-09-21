import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { localeAlternates } from "@/lib/seo";
import { ArrowRight, ArrowDown } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { PagedResult, Service, ServiceCategory } from "@/types";
import { Reveal } from "@/components/public/Reveal";
import { Parallax } from "@/components/public/Parallax";
import { ServiceBreakdown } from "@/components/public/ServiceBreakdown";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "services" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
    alternates: localeAlternates(locale, "/services"),
  };
}

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";

const heroGridStyle = {
  backgroundImage:
    "linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px)",
  backgroundSize: "32px 32px",
} as const;

const surfaceGridStyle = {
  backgroundImage:
    "linear-gradient(to right, rgba(17,28,45,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(17,28,45,0.05) 1px, transparent 1px)",
  backgroundSize: "32px 32px",
} as const;

interface ServicesPageProps {
  searchParams: { categoryId?: string };
}

export default async function ServicesPage({ searchParams }: ServicesPageProps) {
  const t = await getTranslations("services");
  const rawCategoryId = searchParams.categoryId;
  const categoryId =
    rawCategoryId && !Number.isNaN(Number(rawCategoryId)) ? Number(rawCategoryId) : null;

  const categories = await apiFetch<PagedResult<ServiceCategory>>(
    "/service-categories?onlyActive=true&pageSize=50",
  )
    .then((data) => data.items)
    .catch(() => [] as ServiceCategory[]);

  const query = new URLSearchParams({ onlyActive: "true", pageSize: "100" });
  if (categoryId) query.set("categoryId", String(categoryId));

  const services = await apiFetch<PagedResult<Service>>(`/services?${query.toString()}`)
    .then((data) => data.items)
    .catch(() => [] as Service[]);

  return (
    <div>
      {/* Hero */}
      <section className="relative overflow-hidden bg-[#111C2D] pb-24 pt-32 md:pb-32 md:pt-48">
        <div className="absolute inset-0" style={heroGridStyle} />
        <div className="absolute inset-0">
          <Parallax speed={0.08} className="h-full w-full">
            <div
              className="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity"
              style={{ backgroundImage: `url('${HERO_IMAGE}')` }}
            />
          </Parallax>
        </div>
        <div className="absolute inset-0 bg-gradient-to-b from-[#111C2D]/80 to-[#111C2D]/50" />

        <div className="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
          <span className="mb-6 inline-block bg-[#BA1A1A] px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white">
            {t("eyebrow", { year: new Date().getFullYear() })}
          </span>
          <h1 className="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase italic leading-tight tracking-tighter text-white md:text-7xl">
            {t("title1")} <span className="not-italic text-[#BA1A1A]">{t("titleAccent")}</span>
          </h1>
          <p className="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl">
            {t("subtitle")}
          </p>
          <a
            href="#core"
            className="group inline-flex items-center gap-2 border-2 border-[#111C2D] bg-white px-10 py-4 font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D] shadow-[6px_6px_0px_0px_#0059BB] transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_#0059BB]"
          >
            {t("exploreCapabilities")}{" "}
            <ArrowDown className="h-5 w-5 transition-transform duration-300 group-hover:translate-y-1" />
          </a>
        </div>
      </section>

      {/* Services (sectors) */}
      {services.length > 0 ? (
        <ServiceBreakdown
          services={services}
          categories={categories}
          activeCategoryId={categoryId}
        />
      ) : (
        <section className="bg-white py-24 md:py-32">
          <div className="container-brilliant text-center">
            <p className="text-lg text-[#4C4546]">{t("noServices")}</p>
          </div>
        </section>
      )}

      {/* CTA */}
      <section className="bg-[#F9F9FF] py-24 md:py-32" style={surfaceGridStyle}>
        <div className="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
          <Reveal from="left">
            <div className="pop-card group border-2 border-[#111C2D] bg-white p-10 text-center shadow-[6px_6px_0px_0px_#111C2D] md:p-14">
              <h2 className="font-headline text-4xl font-bold uppercase italic leading-none tracking-tighter text-[#111C2D] md:text-6xl">
                {t("ctaTitle1")} <span className="not-italic text-[#BA1A1A]">{t("ctaTitleAccent")}</span>
              </h2>
              <p className="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-[#4C4546]">
                {t("ctaText")}
              </p>
              <Link
                href="/contact"
                className="mt-10 inline-flex items-center gap-3 bg-[#BA1A1A] px-10 py-4 font-headline text-base font-bold uppercase tracking-widest text-white shadow-[4px_4px_0px_0px_#111C2D] transition-transform hover:-translate-x-1 hover:-translate-y-1"
              >
                {t("initiateProtocol")}{" "}
                <ArrowRight className="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1.5" />
              </Link>
            </div>
          </Reveal>
        </div>
      </section>
    </div>
  );
}

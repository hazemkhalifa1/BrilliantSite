import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import {
  Factory,
  Building2,
  Warehouse,
  Bird,
  Briefcase,
  Target,
  Gem,
  Handshake,
  ArrowRight,
  Quote,
} from "lucide-react";
import { Reveal } from "@/components/public/Reveal";
import { apiFetch } from "@/lib/api";
import type { PagedResult, TeamMember } from "@/types";
import { SiteImage } from "@/components/ui/SiteImage";
import { initials } from "@/lib/utils";
import { localized } from "@/lib/localize";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "about" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
  };
}

const gridStyle = {
  backgroundImage:
    "linear-gradient(to right, rgba(17,28,45,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(17,28,45,0.05) 1px, transparent 1px)",
  backgroundSize: "40px 40px",
} as const;

export default async function AboutPage() {
  const t = await getTranslations("about");
  const locale = await getLocale();
  const team = await apiFetch<PagedResult<TeamMember>>("/team?onlyActive=true&pageSize=50").catch(
    () => null,
  );
  const founder =
    team?.items.find((member) => member.name.toLowerCase().includes("ayman")) ?? team?.items[0] ?? null;

  const INDUSTRIES = [
    { icon: Factory, title: t("industryManufacturing") },
    { icon: Building2, title: t("industryCommercial") },
    { icon: Warehouse, title: t("industryWarehousing") },
    { icon: Bird, title: t("industryPoultry") },
    { icon: Briefcase, title: t("industryOffices") },
  ];

  const PILLARS = [
    { icon: Target, title: t("pillarPrecisionTitle"), text: t("pillarPrecisionText") },
    { icon: Gem, title: t("pillarInnovationTitle"), text: t("pillarInnovationText") },
    { icon: Handshake, title: t("pillarPartnershipTitle"), text: t("pillarPartnershipText") },
  ];

  const STATS = [
    { value: "2009", label: t("statEstablished") },
    { value: "74+", label: t("statClients") },
    { value: "24+", label: t("statExperience") },
    { value: "100%", label: t("statTurnkey") },
  ];

  return (
    <div>
      {/* Hero */}
      <section className="relative flex min-h-[520px] items-center overflow-hidden bg-[#111C2D]">
        <div
          className="absolute inset-0 bg-cover bg-center"
          style={{
            backgroundImage:
              "url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80')",
          }}
        />
        <div className="absolute inset-0 bg-[#111C2D]/80" />
        <div
          className="absolute inset-0 opacity-20"
          style={{
            backgroundImage:
              "linear-gradient(to right, rgba(255,255,255,0.06) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.06) 1px, transparent 1px)",
            backgroundSize: "40px 40px",
          }}
        />
        <div className="container-brilliant relative z-10 py-24">
          <div className="max-w-3xl">
            <span className="mb-6 inline-block bg-[#BA1A1A] px-4 py-1 font-headline text-sm uppercase tracking-tighter text-white">
              {t("heroEyebrow")}
            </span>
            <h1 className="font-headline text-6xl uppercase italic leading-tight tracking-tighter text-white md:text-7xl">
              {t("heroTitle")} <span className="not-italic text-[#BA1A1A]">{t("heroAccent")}</span>
            </h1>
            <p className="mt-8 max-w-xl border-s-4 border-[#0059BB] ps-8 text-lg leading-relaxed text-slate-300">
              {t("heroSubtitle")}
            </p>
          </div>
        </div>
        <div className="absolute bottom-12 end-12 hidden font-headline text-4xl uppercase tracking-widest text-[#0059BB] opacity-70 lg:block">
          {t("est")}
        </div>
      </section>

      {/* Who We Are */}
      <section className="bg-white py-24 md:py-32">
        <div className="container-brilliant">
          <Reveal>
            <div className="flex items-center gap-4">
              <div className="h-1 w-12 bg-[#0059BB]" />
              <span className="font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D]">
                {t("whoWeAre")}
              </span>
            </div>
            <h2 className="mt-6 font-headline text-4xl uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl">
              {t("whoWeAreTitle1")} <span className="italic text-[#BA1A1A]">{t("whoWeAreTitleAccent")}</span>
            </h2>
          </Reveal>
          <Reveal delay={100}>
            <div className="mt-10 space-y-6 text-lg leading-relaxed text-[#4C4546]">
              <p>
                {t("p1")}
              </p>
              <p>
                {t.rich("p2", { strong: (chunks) => <strong className="text-[#111C2D]">{chunks}</strong> })}
              </p>
              <p>
                {t.rich("p3", { strong: (chunks) => <strong className="text-[#111C2D]">{chunks}</strong> })}
              </p>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Industries We Serve */}
      <section className="border-y-2 border-[#111C2D] bg-[#F9F9FF] py-24 md:py-32" style={gridStyle}>
        <div className="container-brilliant">
          <Reveal>
            <div className="mx-auto mb-16 max-w-2xl text-center">
              <h2 className="font-headline text-4xl uppercase tracking-tighter text-[#111C2D] md:text-5xl">
                {t("industries")} <span className="italic text-[#BA1A1A]">{t("industriesAccent")}</span>
              </h2>
              <div className="mx-auto mt-6 h-1 w-24 bg-[#0059BB]" />
              <p className="mt-6 text-lg text-[#4C4546]">
                {t("industriesSubtitle")}
              </p>
            </div>
          </Reveal>
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {INDUSTRIES.map((industry, index) => (
              <Reveal key={industry.title} delay={index * 80}>
                <div className="group flex h-full flex-col border-2 border-[#111C2D] bg-white p-8 transition-transform duration-300 hover:-translate-y-2">
                  <div className="mb-6 flex h-14 w-14 items-center justify-center bg-[#0059BB] text-white transition-colors group-hover:bg-[#BA1A1A]">
                    <industry.icon className="h-7 w-7" />
                  </div>
                  <h3 className="font-headline text-xl font-bold uppercase leading-snug tracking-tighter text-[#111C2D]">
                    {industry.title}
                  </h3>
                </div>
              </Reveal>
            ))}
  
              <div className="flex h-full flex-col justify-center border-2 border-[#BA1A1A] bg-[#BA1A1A] p-8">
                <h3 className="font-headline text-xl font-bold uppercase leading-snug tracking-tighter text-white">
                  {t("andMore")}
                </h3>
                <Link
                  href="/contact"
                  className="mt-6 inline-flex items-center gap-2 font-headline text-sm font-bold uppercase tracking-widest text-white transition-opacity hover:opacity-80"
                >
                  {t("discussProject")} <ArrowRight className="h-4 w-4" />
                </Link>
              </div>

          </div>
        </div>
      </section>

      {/* What Sets Us Apart */}
      <section className="relative bg-[#111C2D] py-24 md:py-32">
        <div
          className="absolute inset-0 bg-cover bg-center opacity-25"
          style={{
            backgroundImage:
              "url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1920&q=80')",
          }}
        />
        <div className="absolute inset-0 bg-[#111C2D]/75" />
        <div className="container-brilliant relative z-10">
          <Reveal>
            <div className="mb-16 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
              <div>
                <div className="flex items-center gap-4">
                  <div className="h-1 w-12 bg-[#0059BB]" />
                  <span className="font-headline text-sm font-bold uppercase tracking-widest text-white">
                    {t("apartEyebrow")}
                  </span>
                </div>
                <h2 className="mt-6 font-headline text-4xl uppercase leading-tight tracking-tighter text-white md:text-5xl">
                  {t("apartTitle1")} <span className="italic text-[#BA1A1A]">{t("apartTitleAccent")}</span>
                  {t("apartTitle2")}
                </h2>
              </div>
            </div>
          </Reveal>
          <Reveal delay={100}>
            <p className="mb-14 max-w-3xl text-lg leading-relaxed text-slate-300">
              {t("apartText")}
            </p>
          </Reveal>
          <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
            {PILLARS.map((pillar, index) => (
              <Reveal key={pillar.title} delay={index * 100}>
                <div className="group flex h-full flex-col border-2 border-white/20 bg-white/5 p-10 transition-colors hover:border-[#BA1A1A]">
                  <div className="mb-8 flex h-16 w-16 items-center justify-center bg-[#0059BB] text-white">
                    <pillar.icon className="h-8 w-8" />
                  </div>
                  <h3 className="font-headline text-2xl uppercase italic tracking-tighter text-white">
                    {pillar.title}
                  </h3>
                  <p className="mt-4 leading-relaxed text-slate-300">{pillar.text}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Stats + Mission */}
      <section className="bg-white py-24 md:py-32">
        <div className="container-brilliant">
          <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
            {STATS.map((stat, index) => (
              <Reveal key={stat.label} delay={index * 80}>
                <div className="border-2 border-[#111C2D] bg-white p-8 text-center shadow-[4px_4px_0px_0px_#111C2D]">
                  <p
                    className={`font-headline text-4xl font-bold md:text-5xl ${
                      index === STATS.length - 1 ? "text-[#BA1A1A]" : "text-[#0059BB]"
                    }`}
                  >
                    <span dir="ltr">{stat.value}</span>
                  </p>
                  <p className="mt-2 font-headline text-xs font-bold uppercase tracking-widest text-[#4C4546]">
                    {stat.label}
                  </p>
                </div>
              </Reveal>
            ))}
          </div>

          <Reveal delay={150}>
            <div className="mx-auto mt-24 max-w-4xl text-center">
              <Quote className="mx-auto h-12 w-12 text-[#0059BB]" />
              <h2 className="mt-8 font-headline text-4xl uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl">
                {t("mission")}
              </h2>
              <p className="mt-8 text-2xl font-light italic leading-relaxed text-[#4C4546]">
                {t("missionText")}
              </p>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Founder */}
      <section className="border-y-2 border-[#111C2D] bg-[#F9F9FF] py-24 md:py-32">
        <div className="container-brilliant">
          <div className="grid items-center gap-12 lg:grid-cols-2">
            <Reveal>
              <div className="relative overflow-hidden border-2 border-[#111C2D] bg-[#111C2D] text-white shadow-[6px_6px_0px_0px_#0059BB]">
                <div className="absolute right-4 top-4 font-headline text-8xl italic leading-none text-white opacity-10">
                  {founder ? initials(localized(locale, founder.name, founder.nameAr)) : "AG"}
                </div>
                <div className="relative z-10 flex flex-col md:flex-row">
                  {founder?.imagePath && (
                    <div className="w-full shrink-0 md:w-72">
                      <SiteImage
                        src={founder.imagePath}
                        alt={localized(locale, founder.name, founder.nameAr)}
                        className="h-64 w-full object-cover md:h-full"
                      />
                    </div>
                  )}
                  <div className="p-8 md:p-12">
                    <h3 className="font-headline text-4xl uppercase leading-tight tracking-tighter">
                      {founder ? localized(locale, founder.name, founder.nameAr) : t("founderName")}
                    </h3>
                    <p className="mt-4 font-headline text-sm font-bold uppercase tracking-widest text-[#BA1A1A]">
                      {founder ? localized(locale, founder.jobTitle, founder.jobTitleAr) : t("founderTitle")}
                    </p>
                    <p className="mt-6 leading-relaxed text-slate-300">
                      {t("founderText")}
                    </p>
                  </div>
                </div>
              </div>
            </Reveal>
            <Reveal delay={100}>
              <div>
                <div className="flex items-center gap-4">
                  <div className="h-1 w-12 bg-[#0059BB]" />
                  <span className="font-headline text-sm font-bold uppercase tracking-widest text-[#111C2D]">
                    {t("leadership")}
                  </span>
                </div>
                <h2 className="mt-6 font-headline text-4xl uppercase leading-tight tracking-tighter text-[#111C2D] md:text-5xl">
                  {t("leadershipTitle1")} <span className="italic text-[#BA1A1A]">{t("leadershipTitleAccent")}</span>
                </h2>
                <p className="mt-6 text-lg leading-relaxed text-[#4C4546]">
                  {t("leadershipText")}
                </p>
                <Link
                  href="/team"
                  className="mt-10 inline-flex items-center gap-2 border-2 border-[#0059BB] px-10 py-3 font-headline text-sm font-bold uppercase tracking-widest text-[#0059BB] transition-all hover:-translate-x-1 hover:-translate-y-1 hover:bg-[#0059BB] hover:text-white hover:shadow-[4px_4px_0px_0px_#111C2D]"
                >
                  {t("viewFullTeam")} <ArrowRight className="h-4 w-4" />
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="relative py-40" style={gridStyle}>
        <div
          className="absolute inset-0 bg-cover bg-center opacity-20"
          style={{
            backgroundImage:
              "url('https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1920&q=80')",
          }}
        />
        <div className="container-brilliant relative z-10 text-center">
          <Reveal>
            <h2 className="font-headline text-5xl uppercase italic leading-none tracking-tighter text-[#111C2D] md:text-7xl">
              {t("ctaTitle1")} <span className="not-italic text-[#BA1A1A]">{t("ctaTitleAccent")}</span>
            </h2>
            <p className="mx-auto mt-8 max-w-2xl text-xl leading-relaxed text-[#4C4546]">
              {t("ctaText")}
            </p>
            <div className="mt-12 flex flex-wrap justify-center gap-8">
              <Link
                href="/contact"
                className="bg-[#BA1A1A] px-12 py-5 font-headline text-sm uppercase tracking-widest text-white transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#111C2D]"
              >
                {t("startConsultation")}
              </Link>
              <Link
                href="/services"
                className="border-2 border-[#0059BB] px-12 py-5 font-headline text-sm uppercase tracking-widest text-[#0059BB] transition-all hover:bg-[#0059BB] hover:text-white"
              >
                {t("exploreServices")}
              </Link>
            </div>
          </Reveal>
        </div>
      </section>
    </div>
  );
}

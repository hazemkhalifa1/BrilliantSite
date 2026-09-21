import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { localized } from "@/lib/localize";
import { SITE_URL, localeAlternates } from "@/lib/seo";
import { ArrowRight, Building2, MoveRight, Settings, Zap } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { HeroSection, PagedResult, Project, Service, Testimonial } from "@/types";
import { Reveal } from "@/components/public/Reveal";
import { SiteImage } from "@/components/ui/SiteImage";
import { CountUp } from "@/components/public/CountUp";
import { Parallax } from "@/components/public/Parallax";
import { TiltCard } from "@/components/public/TiltCard";
import { FaqAccordion } from "@/components/public/FaqAccordion";
import { getImageUrl, initials } from "@/lib/utils";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const meta = await getTranslations({ locale, namespace: "metadata" });
  const t = await getTranslations({ locale, namespace: "home" });
  return {
    title: meta("title"),
    description: t("metadataDescription"),
    alternates: localeAlternates(locale),
  };
}

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";

const ABOUT_IMAGE =
  "https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1200&q=80";

const DEFAULT_METRICS: [string, string][] = [
  ["PRECISION_INDEX", "99.98%"],
  ["LOAD_THRESHOLD", "4500kN/m²"],
  ["GEO_COORDS", "40.7128° N, 74.0060° W"],
];

const SERVICE_ICONS = [
  { icon: Settings, bg: "bg-secondary" },
  { icon: Zap, bg: "bg-tertiary" },
  { icon: Building2, bg: "bg-primary" },
];

async function getHomeData() {
  const hero = await apiFetch<HeroSection>("/hero").catch(() => null);
  const services = await apiFetch<PagedResult<Service>>("/services?onlyActive=true&pageSize=6")
    .then((data) => data.items)
    .catch(() => [] as Service[]);
  const projects = await apiFetch<PagedResult<Project>>("/projects?onlyActive=true&pageSize=3")
    .then((data) => data.items)
    .catch(() => [] as Project[]);
  const testimonials = await apiFetch<PagedResult<Testimonial>>("/testimonials?onlyActive=true&pageSize=6")
    .then((data) => data.items)
    .catch(() => [] as Testimonial[]);

  return { hero, services, projects, testimonials };
}

export default async function HomePage() {
  const t = await getTranslations("home");
  const locale = await getLocale();
  const { hero, services, projects, testimonials } = await getHomeData();

  const metrics: [string, string][] =
    hero?.stats && hero.stats.length > 0
      ? hero.stats.map((stat) => [localized(locale, stat.label, stat.labelAr).toUpperCase(), stat.value])
      : DEFAULT_METRICS;

  const faq = t.raw("faq") as {
    eyebrow: string;
    title: string;
    text: string;
    items: { q: string; a: string }[];
  };

  return (
    <div>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Organization",
            name: "Brilliant Engineering Co.",
            url: SITE_URL,
            logo: `${SITE_URL}/logo.png`,
            foundingDate: "2009",
            description:
              "General contracting company specializing in turnkey projects, MEP works, and civil construction in Egypt.",
            contactPoint: {
              "@type": "ContactPoint",
              telephone: "+20-122-901-7782",
              contactType: "customer service",
              availableLanguage: ["Arabic", "English"],
            },
            sameAs: [
              "https://www.facebook.com/brillianteng",
              "https://www.linkedin.com/company/brilliant-engineering",
            ],
          }),
        }}
      />
      {/* Hero */}
      <header className="relative flex min-h-[85vh] w-full flex-col overflow-hidden border-b-4 border-black">
        <div className="absolute inset-0 overflow-hidden">
          <Parallax speed={0.12} className="absolute inset-x-0 -inset-y-[20%]">
            <div
              className="h-full w-full bg-cover bg-center"
              style={{ backgroundImage: `url('${HERO_IMAGE}')` }}
            />
          </Parallax>
        </div>
        <div className="absolute inset-0 hero-gradient" />

        <div className="relative z-10 flex flex-1 flex-col justify-center pt-32 pb-24 container-brilliant">
          <Reveal className="max-w-3xl">
            <div className="mb-6 inline-block bg-secondary px-3 py-1 font-headline text-sm font-bold uppercase tracking-tighter text-white">
              {t("eyebrow")}
            </div>
          </Reveal>
          <Reveal className="max-w-4xl">
            <h1 className="mb-6 font-headline text-5xl font-bold uppercase italic leading-snug text-white md:text-7xl">
              {localized(locale, hero?.headlineTop || t("defaultHeadlineTop"), hero?.headlineTopAr)}
              <br />
              <span className="mt-2 block text-tertiary md:mt-5">
                {localized(locale, hero?.headlineBottom || t("defaultHeadlineBottom"), hero?.headlineBottomAr)}
              </span>
            </h1>
          </Reveal>
          <Reveal className="max-w-xl">
            <p className="mb-10 max-w-xl border-s-4 border-secondary py-2 ps-6 text-xl text-white">
              {localized(locale, hero?.subText || t("defaultSubtext"), hero?.subTextAr)}
            </p>
          </Reveal>
          <Reveal>
            <div className="flex flex-wrap gap-6">
              <Link
                href={hero?.primaryBtnUrl || "/projects"}
                className="group flex items-center gap-3 border-2 border-black bg-[#BA1A1A] px-10 py-5 font-headline text-lg font-bold uppercase tracking-tighter text-white shadow-[6px_6px_0px_black] transition-all hover:translate-x-[6px] hover:translate-y-[6px] hover:shadow-none"
              >
                {localized(locale, hero?.primaryBtnText || t("defaultPrimaryBtn"), hero?.primaryBtnTextAr)}
                <ArrowRight className="text-xl transition-transform duration-300 group-hover:translate-x-1.5" />
              </Link>
              <Link
                href={hero?.secondaryBtnUrl || "/about"}
                className="border-2 border-black bg-white px-10 py-5 font-headline text-lg font-bold uppercase tracking-tighter text-black shadow-[6px_6px_0px_black] transition-all hover:translate-x-[6px] hover:translate-y-[6px] hover:shadow-none"
              >
                {localized(locale, hero?.secondaryBtnText || t("defaultSecondaryBtn"), hero?.secondaryBtnTextAr)}
              </Link>
            </div>
          </Reveal>
        </div>

        <Reveal delay={40} className="absolute bottom-12 end-4 z-10 hidden lg:block xl:end-12">
          <div className="border-4 border-black bg-white p-6 shadow-[8px_8px_0px_#0059bb]">
            <div className="monospaced space-y-2 text-xs font-bold text-black">
              <p className="text-secondary">{t("metricsLabel")}</p>
              {metrics.map(([label, value]) => (
                <p key={label}>
                  {label}: <CountUp value={value} />
                </p>
              ))}
            </div>
          </div>
        </Reveal>
      </header>

      {/* Services Overview */}
      {services.length > 0 && (
        <section className="border-b-4 border-black bg-white py-24">
          <div className="container-brilliant grid grid-cols-1 items-start gap-16 lg:grid-cols-12">
            <div className="lg:col-span-4 lg:sticky lg:top-32">
              <Reveal>
                <span className="mb-4 block font-headline text-sm font-bold uppercase tracking-widest text-tertiary">
                  {t("servicesEyebrow")}
                </span>
                <h2 className="mb-6 font-headline text-5xl font-bold uppercase leading-none text-black">
                  {t("servicesTitle1")}
                  <br />
                  {t("servicesTitle2")}
                </h2>
                <p className="mb-8 text-lg text-neutral/70">
                  {t("servicesText")}
                </p>
                <div className="monospaced border-s-4 border-black py-2 ps-4 text-sm font-bold text-secondary">
                  {t("servicesEst")}
                </div>
              </Reveal>
            </div>

            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:col-span-8">
              {services.map((service, index) => {
                const style = SERVICE_ICONS[index % SERVICE_ICONS.length];
                const Icon = style.icon;
                return (
                  <Reveal
                    key={service.id}
                    delay={(index % 3) * 100}
                    className={
                      services.length % 2 === 1 && index === services.length - 1
                        ? "md:col-span-2"
                        : ""
                    }
                  >
                    <TiltCard className="h-full">
                      <div className="brutalist-card h-full bg-white p-8">
                        <div
                          className={`mb-8 flex h-14 w-14 items-center justify-center border-2 border-black transition-transform duration-300 group-hover:-rotate-6 group-hover:scale-110 ${style.bg}`}
                        >
                          <Icon className="h-7 w-7 text-white" />
                        </div>
                        <h3 className="mb-4 font-headline text-2xl font-bold uppercase text-black">
                          {localized(locale, service.title, service.titleAr)}
                        </h3>
                        <p className="text-neutral/70">{localized(locale, service.description, service.descriptionAr)}</p>
                      </div>
                    </TiltCard>
                  </Reveal>
                );
              })}
            </div>
          </div>
        </section>
      )}

      {/* About Highlight */}
      <section className="overflow-hidden border-b-4 border-black bg-surface py-24">
        <div className="container-brilliant grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
          <Reveal from="left" className="group relative order-2 lg:order-1">
            <Parallax speed={0.05}>
              <div
                className="mx-auto aspect-square max-w-md border-4 border-black bg-cover bg-center shadow-[16px_16px_0px_#0059bb] transition-transform duration-700 group-hover:scale-[1.03]"
                style={{ backgroundImage: `url('${ABOUT_IMAGE}')` }}
              />
            </Parallax>
            <div className="absolute -left-6 -top-6 -z-10 h-32 w-32 border-2 border-black bg-tertiary transition-transform duration-300 group-hover:translate-x-2 group-hover:-translate-y-2" />
          </Reveal>

          <Reveal from="right" className="order-1 lg:order-2">
            <span className="monospaced mb-6 block text-sm font-bold uppercase tracking-[0.2em] text-tertiary">
              {t("philosophyEyebrow")}
            </span>
            <h2 className="mb-8 font-headline text-5xl font-bold uppercase leading-none text-black">
              {t("philosophyTitle1")}
              <br />
              {t("philosophyTitle2")}
            </h2>
            <div className="space-y-6">
              <p className="border-s-4 border-black ps-6 text-lg italic text-neutral">
                {t("philosophyText1")}
              </p>
              <p className="text-neutral/70">
                {t("philosophyText2")}
              </p>
            </div>
            <div className="mt-12">
              <Link
                href="/about"
                className="group inline-flex items-center gap-4 border-b-4 border-tertiary pb-1 font-headline text-sm font-bold uppercase tracking-tighter text-black"
              >
                {t("readOurStory")}
                <MoveRight className="transition-transform group-hover:translate-x-2" />
              </Link>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Recent Projects */}
      {projects.length > 0 && (
        <section className="border-b-4 border-black bg-white py-24">
          <div className="container-brilliant">
            <Reveal className="mb-16">
              <span className="mb-4 block font-headline text-sm font-bold uppercase tracking-widest text-tertiary">
                {t("portfolioEyebrow")}
              </span>
              <div className="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <h2 className="font-headline text-5xl font-bold uppercase leading-none text-black">
                  {t("portfolioTitle1")}
                  <br />
                  {t("portfolioTitle2")}
                </h2>
                <Link
                  href="/projects"
                  className="group inline-flex items-center gap-3 border-b-4 border-black pb-1 font-headline text-sm font-bold uppercase tracking-tighter text-black transition-colors hover:border-tertiary hover:text-tertiary"
                >
                  {t("viewAllProjects")}
                  <ArrowRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1.5" />
                </Link>
              </div>
            </Reveal>

            <div className="grid grid-cols-1 gap-10 md:grid-cols-3">
              {projects.map((project, index) => (
                <Reveal key={project.id} delay={index * 100}>
                  <Link
                    href={`/projects/${project.id}`}
                    className="group block border-2 border-black bg-white p-4 transition-all duration-300 hover:-translate-y-1 hover:bg-surface-container hover:shadow-[8px_8px_0px_#0059bb]"
                  >
                    <div className="relative mb-6 aspect-[4/3] overflow-hidden border-2 border-black">
                      <div className="absolute inset-0 transition-transform duration-700 group-hover:scale-105">
                        <SiteImage
                          src={project.imagePath}
                          alt={localized(locale, project.title, project.titleAr)}
                          className="h-full w-full"
                          eager
                        />
                      </div>
                      <div className="absolute inset-0 bg-primary opacity-0 transition-opacity group-hover:opacity-40" />
                    </div>
                    <div className="flex items-end justify-between gap-4">
                      <div>
                        <span className="monospaced mb-2 block text-xs font-bold uppercase text-tertiary">
                          {project.typeName || t("projectFallback")}
                        </span>
                        <h4 className="font-headline text-2xl font-bold uppercase text-black">
                          {localized(locale, project.title, project.titleAr)}
                        </h4>
                      </div>
                      <span className="monospaced shrink-0 border-2 border-black bg-white px-2 py-1 text-xs font-bold text-black">
                        {project.year}
                      </span>
                    </div>
                  </Link>
                </Reveal>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Testimonials */}
      {testimonials.length > 0 && (
        <section className="border-b-4 border-black bg-white py-24">
          <div className="container-brilliant">
            <Reveal from="left">
              <span className="mb-4 flex items-center gap-3 font-headline text-sm font-bold uppercase tracking-widest text-tertiary">
                <span className="h-3 w-3 bg-[#BA1A1A]" />
                {t("testimonialsEyebrow")}
              </span>
              <h2 className="mb-6 font-headline text-5xl font-bold uppercase leading-none text-black">
                {t("testimonialsTitle1")}{" "}
                <span className="not-italic text-[#BA1A1A]">{t("testimonialsTitle2")}</span>
              </h2>
            </Reveal>
            <Reveal from="right" delay={140}>
              <p className="mb-16 max-w-2xl text-lg text-neutral/70">{t("testimonialsText")}</p>
            </Reveal>
            <div className="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
              {testimonials.map((item, i) => (
                <Reveal key={item.id} delay={i * 100} from={i % 2 === 0 ? "left" : "right"}>
                  <div className="h-full transition-transform duration-300 hover:-translate-y-1.5 hover:-rotate-1">
                    <div
                      className="pop-card group flex h-full flex-col border-2 border-black bg-white p-8 shadow-[6px_6px_0_0_#000]"
                      style={{ transitionDelay: `${i * 100 + 120}ms` }}
                    >
                      <span className="mb-2 block font-headline text-7xl font-bold leading-none text-[#BA1A1A] transition-transform duration-500 ease-out group-hover:-rotate-12 group-hover:scale-110">
                        &ldquo;
                      </span>
                      <p className="mb-8 flex-1 text-base leading-relaxed text-black">
                        {localized(locale, item.quote, item.quoteAr)}
                      </p>
                      <div className="flex items-center gap-4 border-t-2 border-black pt-6">
                        {item.imagePath ? (
                          <SiteImage
                            src={getImageUrl(item.imagePath)}
                            alt={localized(locale, item.name, item.nameAr)}
                            className="h-12 w-12 rounded-full border-2 border-black object-cover transition-transform duration-300 group-hover:scale-110"
                          />
                        ) : (
                          <span className="flex h-12 w-12 items-center justify-center rounded-full border-2 border-black bg-[#BA1A1A] font-headline text-sm font-bold text-white transition-transform duration-300 group-hover:scale-110">
                            {initials(localized(locale, item.name, item.nameAr))}
                          </span>
                        )}
                        <div>
                          <p className="font-headline text-sm font-bold uppercase text-black">
                            {localized(locale, item.name, item.nameAr)}
                          </p>
                          {item.role && (
                            <p className="monospaced text-xs text-neutral/60">
                              {localized(locale, item.role, item.roleAr)}
                            </p>
                          )}
                        </div>
                      </div>
                    </div>
                  </div>
                </Reveal>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* FAQ */}
      <section className="border-b-4 border-black bg-white py-24">
        <div className="container-brilliant grid gap-12 lg:grid-cols-12">
          <div className="lg:col-span-4">
            <Reveal from="left">
              <span className="mb-4 flex items-center gap-3 font-headline text-sm font-bold uppercase tracking-widest text-tertiary">
                <span className="h-3 w-3 bg-[#BA1A1A]" />
                {faq.eyebrow}
              </span>
              <h2 className="mb-6 font-headline text-5xl font-bold uppercase leading-none text-black">
                {faq.title}
              </h2>
            </Reveal>
            <Reveal from="left" delay={140}>
              <p className="text-lg text-neutral/70">{faq.text}</p>
            </Reveal>
          </div>
          <div className="lg:col-span-8">
            <Reveal delay={100} from="right">
              <FaqAccordion items={faq.items} />
            </Reveal>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="bg-neutral py-16 text-white md:py-20">
        <div className="container-brilliant flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
          <div>
            <h2 className="font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight md:text-5xl">
              {t("ctaTitle")}
            </h2>
            <p className="mt-4 max-w-xl text-white/70">{t("ctaText")}</p>
          </div>
          <Link href="/contact" className="btn-cta">
            {t("ctaButton")}
          </Link>
        </div>
      </section>
    </div>
  );
}

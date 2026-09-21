import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { Link } from "@/src/i18n/navigation";
import { localeAlternates } from "@/lib/seo";
import { apiFetch } from "@/lib/api";
import type { PagedResult, TeamMember } from "@/types";
import { Reveal } from "@/components/public/Reveal";
import { Parallax } from "@/components/public/Parallax";
import { TeamMembers } from "@/components/public/TeamMembers";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "team" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
    alternates: localeAlternates(locale, "/team"),
  };
}

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";

const heroGridStyle = {
  backgroundImage:
    "linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px)",
  backgroundSize: "32px 32px",
} as const;

export default async function TeamPage() {
  const t = await getTranslations("team");
  const footer = await getTranslations("footer");
  const nav = await getTranslations("nav");

  const result = await apiFetch<PagedResult<TeamMember>>("/team?onlyActive=true&pageSize=50")
    .catch(() => ({ items: [] as TeamMember[], totalCount: 0, pageIndex: 1, pageSize: 50 }));

  const members = [...result.items].sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
  const [featured, ...supporting] = members;

  const pillars = [
    { n: "01", title: t("pillarPrecisionTitle"), text: t("pillarPrecisionText") },
    { n: "02", title: t("pillarDeliveryTitle"), text: t("pillarDeliveryText") },
    { n: "03", title: t("pillarSafetyTitle"), text: t("pillarSafetyText") },
  ];

  return (
    <div className="relative">
      <Parallax speed={0.15} className="pointer-events-none absolute inset-0 -z-10">
        <div aria-hidden="true" className="graph-grid h-full w-full opacity-70" />
      </Parallax>
      {/* Hero */}
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

      <section className="py-16 md:py-24">
        <div className="container-brilliant">
          {members.length === 0 ? (
            <div className="neo-empty">{t("updating")}</div>
          ) : (
            <TeamMembers
              featured={featured}
              supporting={supporting}
              tagline={footer("tagline")}
            />
          )}
        </div>
      </section>

      {/* How we work */}
      <section className="border-t-4 border-black bg-surface py-16 md:py-24">
        <div className="container-brilliant grid gap-10 lg:grid-cols-12">
          <div className="lg:col-span-4">
            <Reveal>
              <span className="neo-badge">{t("pillarsEyebrow")}</span>
            </Reveal>
            <Reveal delay={60}>
              <h2 className="mt-6 font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight text-neutral md:text-5xl">
                {t("pillarsTitle")}
              </h2>
            </Reveal>
          </div>
          <div className="lg:col-span-8">
            <div className="neo-pillars">
              {pillars.map((pillar, index) => (
                <Reveal key={pillar.n} delay={index * 80} from={index % 2 === 0 ? "left" : "right"}>
                  <div className="h-full transition-transform duration-300 hover:-translate-y-1.5">
                    <div className="pop-card" style={{ transitionDelay: `${index * 80 + 120}ms` }}>
                      <div className="neo-pillar">
                        <div className="neo-pillar__num">{pillar.n}</div>
                        <div className="neo-pillar__title">{pillar.title}</div>
                        <p className="neo-pillar__text">{pillar.text}</p>
                      </div>
                    </div>
                  </div>
                </Reveal>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="border-t-4 border-black bg-neutral py-16 text-white md:py-20">
        <Reveal>
          <div className="container-brilliant flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
            <div>
              <h2 className="font-headline text-3xl font-bold uppercase leading-[0.95] tracking-tight md:text-5xl">
                {t("ctaTitle")}
              </h2>
              <p className="mt-4 max-w-xl text-white/70">{t("ctaText")}</p>
            </div>
            <Link href="/contact" className="btn-cta">
              {nav("getQuote")}
            </Link>
          </div>
        </Reveal>
      </section>
    </div>
  );
}

import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { apiFetch } from "@/lib/api";
import type { PagedResult, TeamMember } from "@/types";
import { Reveal } from "@/components/public/Reveal";
import { TeamCard } from "@/components/public/TeamCard";

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

  const result = await apiFetch<PagedResult<TeamMember>>("/team?onlyActive=true&pageSize=50")
    .catch(() => ({ items: [] as TeamMember[], totalCount: 0, pageIndex: 1, pageSize: 50 }));

  const members = [...result.items].sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
  const [featured, ...supporting] = members;

  return (
    <div>
      {/* Hero */}
      <section className="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
        <div className="absolute inset-0" style={heroGridStyle} />
        <div className="absolute inset-0">
          <div
            className="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity"
            style={{ backgroundImage: `url('${HERO_IMAGE}')` }}
          />
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
            <div className="card-brilliant bg-neutral-light/40 px-6 py-20 text-center">
              <p className="font-headline text-lg font-semibold uppercase text-neutral">
                {t("updating")}
              </p>
            </div>
          ) : (
            <div className="space-y-10 md:space-y-14">
              {featured && (
                <Reveal>
                  <TeamCard member={featured} variant="featured" tagline={footer("tagline")} />
                </Reveal>
              )}

              {supporting.length > 0 && (
                <div className="grid gap-6 lg:grid-cols-2">
                  {supporting.map((member, index) => (
                    <Reveal key={member.id} delay={index * 80}>
                      <TeamCard member={member} variant="row" />
                    </Reveal>
                  ))}
                </div>
              )}
            </div>
          )}
        </div>
      </section>
    </div>
  );
}

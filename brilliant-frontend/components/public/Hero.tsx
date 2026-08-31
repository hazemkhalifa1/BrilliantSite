import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import type { HeroSection as HeroSectionType } from "@/types";

export async function Hero({ hero }: { hero: HeroSectionType | null }) {
  const t = await getTranslations("home");
  const stats = (hero?.stats || []).filter((stat) => stat.isActive);

  return (
    <section className="relative overflow-hidden bg-neutral text-white">
      <div
        className="pointer-events-none absolute inset-0 opacity-20"
        style={{
          backgroundImage:
            "linear-gradient(135deg, rgba(0,89,187,0.5) 0%, transparent 40%), radial-gradient(circle at 85% 20%, rgba(186,26,26,0.4) 0%, transparent 45%)",
        }}
        aria-hidden="true"
      />
      <div className="container-brilliant relative py-20 md:py-28">
        <span className="block h-[3px] w-20 bg-tertiary" aria-hidden="true" />
        <h1 className="mt-6 max-w-4xl font-headline text-4xl font-bold uppercase leading-[1.05] tracking-tight md:text-6xl">
          {hero?.headlineTop || t("defaultHeadlineTop")}
          <br />
          <span className="text-tertiary">
            {hero?.headlineBottom || t("defaultHeadlineBottom")}
          </span>
        </h1>
        <p className="mt-6 max-w-2xl text-base leading-relaxed text-white/80 md:text-lg">
          {hero?.subText || t("defaultSubtext")}
        </p>

        <div className="mt-8 flex flex-wrap gap-4">
          <Link href={hero?.primaryBtnUrl || "/projects"} className="btn-primary">
            {hero?.primaryBtnText || t("defaultPrimaryBtn")}
          </Link>
          <Link
            href={hero?.secondaryBtnUrl || "/contact"}
            className="btn-secondary border-white text-white hover:bg-white hover:text-neutral"
          >
            {hero?.secondaryBtnText || t("defaultSecondaryBtn")}
          </Link>
        </div>

        {stats.length > 0 && (
          <dl className="mt-14 grid grid-cols-2 gap-px bg-white/15 sm:grid-cols-4">
            {stats.map((stat) => (
              <div key={stat.id} className="bg-neutral px-6 py-6">
                <dd className="font-headline text-3xl font-bold text-tertiary md:text-4xl">
                  {stat.value}
                </dd>
                <dt className="mt-1 font-headline text-xs font-semibold uppercase tracking-widest text-white/70">
                  {stat.label}
                </dt>
              </div>
            ))}
          </dl>
        )}
      </div>
    </section>
  );
}

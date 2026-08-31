import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { apiFetch } from "@/lib/api";
import type { PagedResult, TeamMember } from "@/types";
import { PageHeader } from "@/components/public/PageHeader";
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

export default async function TeamPage() {
  const t = await getTranslations("team");
  const result = await apiFetch<PagedResult<TeamMember>>("/team?onlyActive=true&pageSize=50")
    .catch(() => ({ items: [] as TeamMember[], totalCount: 0, pageIndex: 1, pageSize: 50 }));

  return (
    <div>
      <PageHeader
        eyebrow={t("eyebrow")}
        title={t("title")}
        subtitle={t("subtitle")}
      />

      <section className="py-16 md:py-20">
        <div className="container-brilliant">
          {result.items.length > 0 ? (
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              {result.items.map((member) => (
                <TeamCard key={member.id} member={member} />
              ))}
            </div>
          ) : (
            <p className="text-center text-neutral/60">{t("updating")}</p>
          )}
        </div>
      </section>
    </div>
  );
}

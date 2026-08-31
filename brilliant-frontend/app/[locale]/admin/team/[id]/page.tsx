import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch } from "@/lib/adminApi";
import type { TeamMember } from "@/types";
import { TeamMemberForm } from "../TeamMemberForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.team" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditTeamMemberPageProps {
  params: { id: string };
}

export default async function EditTeamMemberPage({ params }: EditTeamMemberPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const member = await adminFetch<TeamMember>(`/team/${id}`).catch(() => null);
  if (!member) notFound();

  return <TeamMemberForm initial={member} />;
}

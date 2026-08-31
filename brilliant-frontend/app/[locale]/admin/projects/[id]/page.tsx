import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch, adminFetchList } from "@/lib/adminApi";
import type { Project, ProjectType } from "@/types";
import { ProjectForm } from "../ProjectForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.projects" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditProjectPageProps {
  params: { id: string };
}

export default async function EditProjectPage({ params }: EditProjectPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const [project, types] = await Promise.all([
    adminFetch<Project>(`/projects/${id}`).catch(() => null),
    adminFetchList<ProjectType>("/project-types?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ProjectType[]),
  ]);

  if (!project) notFound();

  return <ProjectForm initial={project} types={types} />;
}

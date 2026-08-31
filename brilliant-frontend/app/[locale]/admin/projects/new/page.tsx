import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { adminFetchList } from "@/lib/adminApi";
import type { ProjectType } from "@/types";
import { ProjectForm } from "../ProjectForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.projects" });
  return { title: form("newTitle", { entity: section("entityTitle") }) };
}

export default async function NewProjectPage() {
  const types = await adminFetchList<ProjectType>("/project-types?pageSize=100")
    .then((data) => data.items)
    .catch(() => [] as ProjectType[]);
  return <ProjectForm types={types} />;
}

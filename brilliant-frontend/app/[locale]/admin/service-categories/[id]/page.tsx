import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch } from "@/lib/adminApi";
import type { ServiceCategory } from "@/types";
import { ServiceCategoryForm } from "../ServiceCategoryForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.serviceCategories" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditServiceCategoryPageProps {
  params: { id: string };
}

export default async function EditServiceCategoryPage({ params }: EditServiceCategoryPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const category = await adminFetch<ServiceCategory>(`/service-categories/${id}`).catch(() => null);
  if (!category) notFound();

  return <ServiceCategoryForm initial={category} />;
}

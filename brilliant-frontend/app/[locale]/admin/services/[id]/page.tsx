import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch, adminFetchList } from "@/lib/adminApi";
import type { Service, ServiceCategory } from "@/types";
import { ServiceForm } from "../ServiceForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.services" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditServicePageProps {
  params: { id: string };
}

export default async function EditServicePage({ params }: EditServicePageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const [service, categories] = await Promise.all([
    adminFetch<Service>(`/services/${id}`).catch(() => null),
    adminFetchList<ServiceCategory>("/service-categories?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ServiceCategory[]),
  ]);

  if (!service) notFound();

  return <ServiceForm initial={service} categories={categories} />;
}

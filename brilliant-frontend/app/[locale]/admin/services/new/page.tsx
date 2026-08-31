import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { adminFetchList } from "@/lib/adminApi";
import type { ServiceCategory } from "@/types";
import { ServiceForm } from "../ServiceForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.services" });
  return { title: form("newTitle", { entity: section("entityTitle") }) };
}

export default async function NewServicePage() {
  const categories = await adminFetchList<ServiceCategory>("/service-categories?pageSize=100")
    .then((data) => data.items)
    .catch(() => [] as ServiceCategory[]);
  return <ServiceForm categories={categories} />;
}

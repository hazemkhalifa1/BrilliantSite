import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { ClientForm } from "../ClientForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.clients" });
  return { title: form("newTitle", { entity: section("entityTitle") }) };
}

export default function NewClientPage() {
  return <ClientForm />;
}

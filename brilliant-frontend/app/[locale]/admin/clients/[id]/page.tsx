import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch } from "@/lib/adminApi";
import type { Client } from "@/types";
import { ClientForm } from "../ClientForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.clients" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditClientPageProps {
  params: { id: string };
}

export default async function EditClientPage({ params }: EditClientPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const client = await adminFetch<Client>(`/clients/${id}`).catch(() => null);
  if (!client) notFound();

  return <ClientForm initial={client} />;
}

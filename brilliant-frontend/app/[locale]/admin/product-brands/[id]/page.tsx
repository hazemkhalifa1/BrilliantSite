import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch } from "@/lib/adminApi";
import type { ProductBrand } from "@/types";
import { ProductBrandForm } from "../ProductBrandForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.productBrands" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditProductBrandPageProps {
  params: { id: string };
}

export default async function EditProductBrandPage({ params }: EditProductBrandPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const brand = await adminFetch<ProductBrand>(`/product-brands/${id}`).catch(() => null);
  if (!brand) notFound();

  return <ProductBrandForm initial={brand} />;
}

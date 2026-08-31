import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch, adminFetchList } from "@/lib/adminApi";
import type { ProductBrand, ProductCategory } from "@/types";
import { ProductCategoryForm } from "../ProductCategoryForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.productCategories" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditProductCategoryPageProps {
  params: { id: string };
}

export default async function EditProductCategoryPage({ params }: EditProductCategoryPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const [category, brands] = await Promise.all([
    adminFetch<ProductCategory>(`/product-categories/${id}`).catch(() => null),
    adminFetchList<ProductBrand>("/product-brands?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ProductBrand[]),
  ]);

  if (!category) notFound();

  return <ProductCategoryForm initial={category} brands={brands} />;
}

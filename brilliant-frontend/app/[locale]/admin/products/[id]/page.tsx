import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch, adminFetchList } from "@/lib/adminApi";
import type { Product, ProductBrand, ProductCategory } from "@/types";
import { ProductForm } from "../ProductForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.products" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditProductPageProps {
  params: { id: string };
}

export default async function EditProductPage({ params }: EditProductPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const [product, categories, brands] = await Promise.all([
    adminFetch<Product>(`/products/${id}`).catch(() => null),
    adminFetchList<ProductCategory>("/product-categories?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ProductCategory[]),
    adminFetchList<ProductBrand>("/product-brands?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ProductBrand[]),
  ]);

  if (!product) notFound();

  return <ProductForm initial={product} categories={categories} brands={brands} />;
}

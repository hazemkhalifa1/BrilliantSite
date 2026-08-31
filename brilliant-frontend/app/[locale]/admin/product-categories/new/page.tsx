import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { adminFetchList } from "@/lib/adminApi";
import type { ProductBrand } from "@/types";
import { ProductCategoryForm } from "../ProductCategoryForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.productCategories" });
  return { title: form("newTitle", { entity: section("entityTitle") }) };
}

export default async function NewProductCategoryPage() {
  const brands = await adminFetchList<ProductBrand>("/product-brands?pageSize=100")
    .then((data) => data.items)
    .catch(() => [] as ProductBrand[]);
  return <ProductCategoryForm brands={brands} />;
}

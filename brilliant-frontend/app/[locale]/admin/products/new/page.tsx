import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { adminFetchList } from "@/lib/adminApi";
import type { BlogPost, ProductBrand, ProductCategory } from "@/types";
import { ProductForm } from "../ProductForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.products" });
  return { title: form("newTitle", { entity: section("entityTitle") }) };
}

export default async function NewProductPage() {
  const [categories, brands, blogPosts] = await Promise.all([
    adminFetchList<ProductCategory>("/product-categories?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ProductCategory[]),
    adminFetchList<ProductBrand>("/product-brands?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as ProductBrand[]),
    adminFetchList<BlogPost>("/blog?pageSize=100&publishedOnly=true")
      .then((data) => data.items)
      .catch(() => [] as BlogPost[]),
  ]);
  return <ProductForm categories={categories} brands={brands} blogPosts={blogPosts} />;
}

"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { Product, ProductBrand, ProductCategory } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Select } from "@/components/admin/Select";
import { Toggle } from "@/components/admin/Toggle";
import { ImageUpload } from "@/components/admin/ImageUpload";
import { DocumentUpload } from "@/components/admin/DocumentUpload";

export interface ProductFormProps {
  initial?: Product;
  categories: ProductCategory[];
  brands: ProductBrand[];
}

export function ProductForm({ initial, categories, brands }: ProductFormProps) {
  const router = useRouter();
  const t = useTranslations("admin.form");
  const section = useTranslations("admin.sections.products");
  const isEdit = Boolean(initial);

  const initialBrandId = initial
    ? categories.find((category) => category.id === initial.categoryId)?.brandId
    : undefined;

  const [name, setName] = useState(initial?.name ?? "");
  const [nameAr, setNameAr] = useState(initial?.nameAr ?? "");
  const [description, setDescription] = useState(initial?.description ?? "");
  const [descriptionAr, setDescriptionAr] = useState(initial?.descriptionAr ?? "");
  const [imagePath, setImagePath] = useState<string | null>(initial?.imagePath ?? null);
  const [documentationUrl, setDocumentationUrl] = useState<string | null>(
    initial?.documentationUrl ?? null,
  );
  const [brandId, setBrandId] = useState(initialBrandId ? String(initialBrandId) : "");
  const [categoryId, setCategoryId] = useState(initial ? String(initial.categoryId) : "");
  const [order, setOrder] = useState(initial?.order != null ? String(initial.order) : "0");
  const [isActive, setIsActive] = useState(initial?.isActive ?? true);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const categoryOptions = categories.filter(
    (category) => !brandId || String(category.brandId) === brandId,
  );

  function handleBrandChange(value: string) {
    setBrandId(value);
    setCategoryId("");
  }

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    if (!name.trim()) {
      setError(t("nameRequired"));
      return;
    }
    if (!categoryId) {
      setError(t("selectCategory"));
      return;
    }
    setError(null);
    setSubmitting(true);
    try {
      const body = {
        name: name.trim(),
        nameAr: nameAr.trim() || null,
        description,
        descriptionAr: descriptionAr.trim() || null,
        imagePath: imagePath ?? "",
        documentationUrl: documentationUrl ?? "",
        categoryId: Number(categoryId),
        order: Number(order) || 0,
        isActive,
      };
      if (initial) {
        await apiFetch(`/products/${initial.id}`, {
          method: "PUT",
          body: JSON.stringify({ id: initial.id, ...body }),
        });
      } else {
        await apiFetch("/products", { method: "POST", body: JSON.stringify(body) });
      }
      router.push("/admin/products");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : t("saveFailed", { entity: section("entity") }));
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="mx-auto max-w-2xl space-y-6">
      <Link
        href="/admin/products"
        className="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-neutral/60 transition-colors hover:text-secondary"
      >
        <ArrowLeft className="h-4 w-4" />
        {t("back", { entity: section("title") })}
      </Link>

      <form onSubmit={handleSubmit} className="card-brilliant space-y-5 bg-white p-6">
        <h1 className="font-headline text-xl font-bold uppercase">
          {isEdit
            ? t("editTitle", { entity: section("entityTitle") })
            : t("newTitle", { entity: section("entityTitle") })}
        </h1>

        <Input
          label={t("name")}
          name="name"
          value={name}
          onChange={(event) => setName(event.target.value)}
          placeholder={t("productNamePlaceholder")}
        />

        <Input
          label={t("nameAr")}
          name="nameAr"
          value={nameAr}
          onChange={(event) => setNameAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <TextArea
          label={t("description")}
          name="description"
          rows={6}
          value={description}
          onChange={(event) => setDescription(event.target.value)}
          placeholder={t("descriptionPlaceholder", { entity: section("entity") })}
        />

        <TextArea
          label={t("descriptionAr")}
          name="descriptionAr"
          rows={6}
          value={descriptionAr}
          onChange={(event) => setDescriptionAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <Select
          label={t("brand")}
          name="brandId"
          value={brandId}
          onChange={(event) => handleBrandChange(event.target.value)}
          options={brands.map((brand) => ({
            value: String(brand.id),
            label: brand.name,
          }))}
          placeholder={t("selectBrandPlaceholder")}
        />

        <Select
          label={t("category")}
          name="categoryId"
          value={categoryId}
          onChange={(event) => setCategoryId(event.target.value)}
          options={categoryOptions.map((category) => ({
            value: String(category.id),
            label: category.name,
          }))}
          placeholder={
            brandId
              ? t("selectCategoryForBrandPlaceholder")
              : t("selectBrandFirstPlaceholder")
          }
        />

        <p className="text-xs text-neutral/50">
          {categoryId
            ? t("brandDerivedHint")
            : brandId
              ? t("pickCategoryHint")
              : t("pickBrandHint")}
        </p>

        <ImageUpload entity="products" label={t("productImage")} value={imagePath} onChange={setImagePath} />

        <DocumentUpload label={t("documentationFile")} value={documentationUrl} onChange={setDocumentationUrl} />

        <Input
          label={t("order")}
          name="order"
          type="number"
          min={0}
          value={order}
          onChange={(event) => setOrder(event.target.value)}
          placeholder="0"
        />

        <Toggle
          checked={isActive}
          onChange={setIsActive}
          label={t("active")}
          description={t("inactiveHidden", { entity: section("entity") })}
        />

        {error && <p className="border-s-2 border-tertiary bg-neutral-light px-3 py-2 text-sm text-tertiary">{error}</p>}

        <div className="flex justify-end gap-2 border-t border-line pt-5">
          <Link href="/admin/products">
            <Button type="button" variant="ghost">
              {t("cancel")}
            </Button>
          </Link>
          <Button type="submit" loading={submitting}>
            {submitting
              ? t("saving")
              : isEdit
                ? t("update", { entity: section("entityTitle") })
                : t("create", { entity: section("entityTitle") })}
          </Button>
        </div>
      </form>
    </div>
  );
}

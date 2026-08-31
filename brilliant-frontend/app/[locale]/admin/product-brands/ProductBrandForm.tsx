"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { ProductBrand } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Toggle } from "@/components/admin/Toggle";
import { ImageUpload } from "@/components/admin/ImageUpload";

export interface ProductBrandFormProps {
  initial?: ProductBrand;
}

export function ProductBrandForm({ initial }: ProductBrandFormProps) {
  const router = useRouter();
  const t = useTranslations("admin.form");
  const section = useTranslations("admin.sections.productBrands");
  const isEdit = Boolean(initial);

  const [name, setName] = useState(initial?.name ?? "");
  const [nameAr, setNameAr] = useState(initial?.nameAr ?? "");
  const [description, setDescription] = useState(initial?.description ?? "");
  const [descriptionAr, setDescriptionAr] = useState(initial?.descriptionAr ?? "");
  const [backgroundImagePath, setBackgroundImagePath] = useState<string | null>(
    initial?.backgroundImagePath ?? null,
  );
  const [order, setOrder] = useState(initial?.order != null ? String(initial.order) : "0");
  const [isActive, setIsActive] = useState(initial?.isActive ?? true);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    if (!name.trim()) {
      setError(t("nameRequired"));
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
        backgroundImagePath: backgroundImagePath ?? "",
        order: Number(order) || 0,
        isActive,
      };
      if (initial) {
        await apiFetch(`/product-brands/${initial.id}`, {
          method: "PUT",
          body: JSON.stringify({ id: initial.id, ...body }),
        });
      } else {
        await apiFetch("/product-brands", { method: "POST", body: JSON.stringify(body) });
      }
      router.push("/admin/product-brands");
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
        href="/admin/product-brands"
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
          placeholder={t("brandNamePlaceholder")}
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
          rows={4}
          value={description}
          onChange={(event) => setDescription(event.target.value)}
          placeholder={t("descriptionPlaceholder", { entity: section("entity") })}
        />

        <TextArea
          label={t("descriptionAr")}
          name="descriptionAr"
          rows={4}
          value={descriptionAr}
          onChange={(event) => setDescriptionAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <ImageUpload
          entity="product-brands"
          label={t("backgroundImage")}
          value={backgroundImagePath}
          onChange={setBackgroundImagePath}
        />

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
          <Link href="/admin/product-brands">
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

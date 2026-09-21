"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { BlogPost, Service, ServiceCategory } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Select } from "@/components/admin/Select";
import { Toggle } from "@/components/admin/Toggle";
import { ImageUpload } from "@/components/admin/ImageUpload";

export interface ServiceFormProps {
  initial?: Service;
  categories: ServiceCategory[];
  blogPosts: BlogPost[];
}

export function ServiceForm({ initial, categories, blogPosts }: ServiceFormProps) {
  const router = useRouter();
  const t = useTranslations("admin.form");
  const section = useTranslations("admin.sections.services");
  const isEdit = Boolean(initial);

  const [title, setTitle] = useState(initial?.title ?? "");
  const [titleAr, setTitleAr] = useState(initial?.titleAr ?? "");
  const [description, setDescription] = useState(initial?.description ?? "");
  const [descriptionAr, setDescriptionAr] = useState(initial?.descriptionAr ?? "");
  const [categoryId, setCategoryId] = useState(initial ? String(initial.categoryId) : "");
  const [iconPath, setIconPath] = useState<string | null>(initial?.iconPath ?? null);
  const [order, setOrder] = useState(initial?.order != null ? String(initial.order) : "0");
  const [isActive, setIsActive] = useState(initial?.isActive ?? true);
  const [relatedBlogPostId, setRelatedBlogPostId] = useState(
    initial?.relatedBlogPostId != null ? String(initial.relatedBlogPostId) : "",
  );
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    if (!title.trim()) {
      setError(t("titleRequired"));
      return;
    }
    if (!categoryId) {
      setError(t("selectCategory"));
      return;
    }
    setError(null);
    setSubmitting(true);
    try {
      if (initial) {
        await apiFetch(`/services/${initial.id}`, {
          method: "PUT",
          body: JSON.stringify({
            id: initial.id,
            title: title.trim(),
            titleAr: titleAr.trim() || null,
            description,
            descriptionAr: descriptionAr.trim() || null,
            iconPath: iconPath ?? "",
            categoryId: Number(categoryId),
            order: Number(order) || 0,
            isActive,
            relatedBlogPostId: relatedBlogPostId ? Number(relatedBlogPostId) : null,
          }),
        });
      } else {
        await apiFetch("/services", {
          method: "POST",
          body: JSON.stringify({
            title: title.trim(),
            titleAr: titleAr.trim() || null,
            description,
            descriptionAr: descriptionAr.trim() || null,
            iconPath: iconPath ?? "",
            categoryId: Number(categoryId),
            order: Number(order) || 0,
            isActive,
            relatedBlogPostId: relatedBlogPostId ? Number(relatedBlogPostId) : null,
          }),
        });
      }
      router.push("/admin/services");
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
        href="/admin/services"
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
          label={t("title")}
          name="title"
          value={title}
          onChange={(event) => setTitle(event.target.value)}
          placeholder={t("titlePlaceholder")}
        />

        <Input
          label={t("titleAr")}
          name="titleAr"
          value={titleAr}
          onChange={(event) => setTitleAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <TextArea
          label={t("description")}
          name="description"
          rows={5}
          value={description}
          onChange={(event) => setDescription(event.target.value)}
          placeholder={t("descriptionPlaceholder", { entity: section("entity") })}
        />

        <TextArea
          label={t("descriptionAr")}
          name="descriptionAr"
          rows={5}
          value={descriptionAr}
          onChange={(event) => setDescriptionAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <Select
          label={t("category")}
          name="categoryId"
          value={categoryId}
          onChange={(event) => setCategoryId(event.target.value)}
          options={categories.map((category) => ({
            value: String(category.id),
            label: category.name,
          }))}
          placeholder={t("selectCategoryPlaceholder")}
        />

        <ImageUpload
          entity="services"
          label={t("iconImage")}
          value={iconPath}
          onChange={setIconPath}
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

        <Select
          label={t("relatedBlogPost")}
          name="relatedBlogPostId"
          value={relatedBlogPostId}
          onChange={(event) => setRelatedBlogPostId(event.target.value)}
          options={blogPosts.map((post) => ({
            value: String(post.id),
            label: post.title,
          }))}
          placeholder={t("none")}
        />

        <Toggle
          checked={isActive}
          onChange={setIsActive}
          label={t("active")}
          description={t("inactiveHidden", { entity: section("entity") })}
        />

        {error && <p className="border-s-2 border-tertiary bg-neutral-light px-3 py-2 text-sm text-tertiary">{error}</p>}

        <div className="flex justify-end gap-2 border-t border-line pt-5">
          <Link href="/admin/services">
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

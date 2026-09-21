"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { BlogPost, Tag } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Toggle } from "@/components/admin/Toggle";
import { ImageUpload } from "@/components/admin/ImageUpload";
import { RichTextEditor } from "@/components/admin/RichTextEditor";
import { TagPicker } from "@/components/admin/TagPicker";

export interface BlogPostFormProps {
  initial?: BlogPost;
  tags: Tag[];
}

export function BlogPostForm({ initial, tags }: BlogPostFormProps) {
  const router = useRouter();
  const t = useTranslations("admin.form");
  const section = useTranslations("admin.sections.blog");
  const isEdit = Boolean(initial);

  const [title, setTitle] = useState(initial?.title ?? "");
  const [titleAr, setTitleAr] = useState(initial?.titleAr ?? "");
  const [content, setContent] = useState(initial?.content ?? "");
  const [contentAr, setContentAr] = useState(initial?.contentAr ?? "");
  const [coverImagePath, setCoverImagePath] = useState<string | null>(initial?.coverImagePath ?? null);
  const [metaTitle, setMetaTitle] = useState(initial?.metaTitle ?? "");
  const [metaTitleAr, setMetaTitleAr] = useState(initial?.metaTitleAr ?? "");
  const [metaDescription, setMetaDescription] = useState(initial?.metaDescription ?? "");
  const [metaDescriptionAr, setMetaDescriptionAr] = useState(initial?.metaDescriptionAr ?? "");
  const [tagIds, setTagIds] = useState<number[]>(initial?.tags?.map((tag) => tag.id) ?? []);
  const [order, setOrder] = useState(initial?.order != null ? String(initial.order) : "0");
  const [isPublished, setIsPublished] = useState(initial?.isPublished ?? false);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    if (!title.trim()) {
      setError(t("titleRequired"));
      return;
    }
    setError(null);
    setSubmitting(true);
    try {
      const body = {
        title: title.trim(),
        titleAr: titleAr.trim() || null,
        content,
        contentAr: contentAr.trim() || null,
        coverImagePath: coverImagePath ?? "",
        metaTitle,
        metaTitleAr: metaTitleAr.trim() || null,
        metaDescription,
        metaDescriptionAr: metaDescriptionAr.trim() || null,
        tagIds,
        order: Number(order) || 0,
        isPublished,
      };
      if (initial) {
        await apiFetch(`/blog/${initial.id}`, {
          method: "PUT",
          body: JSON.stringify({ id: initial.id, ...body }),
        });
      } else {
        await apiFetch("/blog", { method: "POST", body: JSON.stringify(body) });
      }
      if (body.isPublished) {
        fetch("/api/seo/ping", { method: "POST", keepalive: true }).catch(() => {});
      }
      router.push("/admin/blog");
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : t("saveFailed", { entity: section("entity") }));
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="mx-auto max-w-3xl space-y-6">
      <Link
        href="/admin/blog"
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
          placeholder={t("postTitlePlaceholder")}
        />
        <Input
          label={t("titleAr")}
          name="titleAr"
          value={titleAr}
          onChange={(event) => setTitleAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <RichTextEditor
          label={t("content")}
          value={content}
          onChange={setContent}
          placeholder={t("contentPlaceholder")}
        />
        <RichTextEditor
          label={t("contentAr")}
          value={contentAr}
          onChange={setContentAr}
          placeholder={t("arPlaceholder")}
        />

        <ImageUpload entity="blog" label={t("coverImage")} value={coverImagePath} onChange={setCoverImagePath} />

        <div className="border-t border-line pt-5">
          <h2 className="font-headline text-sm font-bold uppercase text-neutral/70">{t("seo")}</h2>
          <div className="mt-4 space-y-5">
            <Input
              label={t("metaTitle")}
              name="metaTitle"
              value={metaTitle}
              onChange={(event) => setMetaTitle(event.target.value)}
              placeholder={t("metaTitlePlaceholder")}
            />
            <Input
              label={t("metaTitleAr")}
              name="metaTitleAr"
              value={metaTitleAr}
              onChange={(event) => setMetaTitleAr(event.target.value)}
              placeholder={t("arPlaceholder")}
            />
            <TextArea
              label={t("metaDescription")}
              name="metaDescription"
              rows={3}
              value={metaDescription}
              onChange={(event) => setMetaDescription(event.target.value)}
              placeholder={t("metaDescriptionPlaceholder")}
            />
            <TextArea
              label={t("metaDescriptionAr")}
              name="metaDescriptionAr"
              rows={3}
              value={metaDescriptionAr}
              onChange={(event) => setMetaDescriptionAr(event.target.value)}
              placeholder={t("arPlaceholder")}
            />
          </div>
        </div>

        <div className="border-t border-line pt-5">
          <TagPicker label={t("tags")} options={tags} value={tagIds} onChange={setTagIds} />
        </div>

        <div className="border-t border-line pt-5">
          <Input
            label={t("order")}
            name="order"
            type="number"
            min={0}
            value={order}
            onChange={(event) => setOrder(event.target.value)}
            placeholder="0"
          />
        </div>

        <div className="border-t border-line pt-5">
          <Toggle
            checked={isPublished}
            onChange={setIsPublished}
            label={t("publish")}
            description={t("publishDescription")}
          />
        </div>

        {error && <p className="border-s-2 border-tertiary bg-neutral-light px-3 py-2 text-sm text-tertiary">{error}</p>}

        <div className="flex justify-end gap-2 border-t border-line pt-5">
          <Link href="/admin/blog">
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

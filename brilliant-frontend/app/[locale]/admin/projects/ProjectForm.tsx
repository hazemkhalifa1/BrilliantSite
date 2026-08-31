"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { Project, ProjectType } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Select } from "@/components/admin/Select";
import { Toggle } from "@/components/admin/Toggle";
import { ImageUpload } from "@/components/admin/ImageUpload";

export interface ProjectFormProps {
  initial?: Project;
  types: ProjectType[];
}

export function ProjectForm({ initial, types }: ProjectFormProps) {
  const router = useRouter();
  const t = useTranslations("admin.form");
  const section = useTranslations("admin.sections.projects");
  const isEdit = Boolean(initial);

  const [title, setTitle] = useState(initial?.title ?? "");
  const [titleAr, setTitleAr] = useState(initial?.titleAr ?? "");
  const [description, setDescription] = useState(initial?.description ?? "");
  const [descriptionAr, setDescriptionAr] = useState(initial?.descriptionAr ?? "");
  const [imagePath, setImagePath] = useState<string | null>(initial?.imagePath ?? null);
  const [clientName, setClientName] = useState(initial?.clientName ?? "");
  const [clientNameAr, setClientNameAr] = useState(initial?.clientNameAr ?? "");
  const [year, setYear] = useState(initial?.year ? String(initial.year) : String(new Date().getFullYear()));
  const [typeId, setTypeId] = useState(initial ? String(initial.typeId) : "");
  const [order, setOrder] = useState(initial?.order != null ? String(initial.order) : "0");
  const [isActive, setIsActive] = useState(initial?.isActive ?? true);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    if (!title.trim()) {
      setError(t("titleRequired"));
      return;
    }
    if (!typeId) {
      setError(t("selectType"));
      return;
    }
    const yearValue = Number(year);
    if (Number.isNaN(yearValue) || yearValue < 1900 || yearValue > 2100) {
      setError(t("validYear"));
      return;
    }
    setError(null);
    setSubmitting(true);
    try {
      const body = {
        title: title.trim(),
        titleAr: titleAr.trim() || null,
        description,
        descriptionAr: descriptionAr.trim() || null,
        imagePath: imagePath ?? "",
        clientName,
        clientNameAr: clientNameAr.trim() || null,
        year: yearValue,
        typeId: Number(typeId),
        order: Number(order) || 0,
        isActive,
      };
      if (initial) {
        await apiFetch(`/projects/${initial.id}`, {
          method: "PUT",
          body: JSON.stringify({ id: initial.id, ...body }),
        });
      } else {
        await apiFetch("/projects", { method: "POST", body: JSON.stringify(body) });
      }
      router.push("/admin/projects");
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
        href="/admin/projects"
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
          label={t("projectTitle")}
          name="title"
          value={title}
          onChange={(event) => setTitle(event.target.value)}
          placeholder={t("projectTitlePlaceholder")}
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

        <div className="grid gap-5 sm:grid-cols-2">
          <Input
            label={t("clientName")}
            name="clientName"
            value={clientName}
            onChange={(event) => setClientName(event.target.value)}
            placeholder={t("clientNamePlaceholder")}
          />
          <Input
            label={t("clientNameAr")}
            name="clientNameAr"
            value={clientNameAr}
            onChange={(event) => setClientNameAr(event.target.value)}
            placeholder={t("arPlaceholder")}
          />
          <Input
            label={t("year")}
            name="year"
            type="number"
            min={1900}
            max={2100}
            value={year}
            onChange={(event) => setYear(event.target.value)}
          />
        </div>

        <Select
          label={t("projectType")}
          name="typeId"
          value={typeId}
          onChange={(event) => setTypeId(event.target.value)}
          options={types.map((type) => ({ value: String(type.id), label: type.name }))}
          placeholder={t("selectTypePlaceholder")}
        />

        <ImageUpload entity="projects" label={t("projectImage")} value={imagePath} onChange={setImagePath} />

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
          <Link href="/admin/projects">
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

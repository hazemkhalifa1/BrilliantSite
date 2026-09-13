"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { TeamMember } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Toggle } from "@/components/admin/Toggle";
import { ImageUpload } from "@/components/admin/ImageUpload";

export interface TeamMemberFormProps {
  initial?: TeamMember;
}

export function TeamMemberForm({ initial }: TeamMemberFormProps) {
  const router = useRouter();
  const t = useTranslations("admin.form");
  const section = useTranslations("admin.sections.team");
  const isEdit = Boolean(initial);

  const [name, setName] = useState(initial?.name ?? "");
  const [nameAr, setNameAr] = useState(initial?.nameAr ?? "");
  const [jobTitle, setJobTitle] = useState(initial?.jobTitle ?? "");
  const [jobTitleAr, setJobTitleAr] = useState(initial?.jobTitleAr ?? "");
  const [description, setDescription] = useState(initial?.description ?? "");
  const [descriptionAr, setDescriptionAr] = useState(initial?.descriptionAr ?? "");
  const [imagePath, setImagePath] = useState<string | null>(initial?.imagePath ?? null);
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
        jobTitle,
        jobTitleAr: jobTitleAr.trim() || null,
        description,
        descriptionAr: descriptionAr.trim() || null,
        imagePath: imagePath ?? "",
        isActive,
      };
      if (initial) {
        await apiFetch(`/team/${initial.id}`, {
          method: "PUT",
          body: JSON.stringify({ id: initial.id, ...body }),
        });
      } else {
        await apiFetch("/team", { method: "POST", body: JSON.stringify(body) });
      }
      router.push("/admin/team");
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
        href="/admin/team"
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
          placeholder={t("teamMemberNamePlaceholder")}
        />

        <Input
          label={t("nameAr")}
          name="nameAr"
          value={nameAr}
          onChange={(event) => setNameAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <Input
          label={t("jobTitle")}
          name="jobTitle"
          value={jobTitle}
          onChange={(event) => setJobTitle(event.target.value)}
          placeholder={t("jobTitlePlaceholder")}
        />

        <Input
          label={t("jobTitleAr")}
          name="jobTitleAr"
          value={jobTitleAr}
          onChange={(event) => setJobTitleAr(event.target.value)}
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

        <ImageUpload entity="team" label={t("photo")} value={imagePath} onChange={setImagePath} />

        <Toggle
          checked={isActive}
          onChange={setIsActive}
          label={t("active")}
          description={t("inactiveHidden", { entity: section("entity") })}
        />

        {error && <p className="border-s-2 border-tertiary bg-neutral-light px-3 py-2 text-sm text-tertiary">{error}</p>}

        <div className="flex justify-end gap-2 border-t border-line pt-5">
          <Link href="/admin/team">
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

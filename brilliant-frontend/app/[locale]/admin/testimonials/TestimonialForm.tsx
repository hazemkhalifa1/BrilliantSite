"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { ArrowLeft } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { Testimonial } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Toggle } from "@/components/admin/Toggle";
import { ImageUpload } from "@/components/admin/ImageUpload";

export interface TestimonialFormProps {
  initial?: Testimonial;
}

export function TestimonialForm({ initial }: TestimonialFormProps) {
  const router = useRouter();
  const t = useTranslations("admin.form");
  const section = useTranslations("admin.sections.testimonials");
  const isEdit = Boolean(initial);

  const [name, setName] = useState(initial?.name ?? "");
  const [nameAr, setNameAr] = useState(initial?.nameAr ?? "");
  const [quote, setQuote] = useState(initial?.quote ?? "");
  const [quoteAr, setQuoteAr] = useState(initial?.quoteAr ?? "");
  const [role, setRole] = useState(initial?.role ?? "");
  const [roleAr, setRoleAr] = useState(initial?.roleAr ?? "");
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
    if (!quote.trim()) {
      setError(t("quoteRequired"));
      return;
    }
    setError(null);
    setSubmitting(true);
    try {
      const body = {
        name: name.trim(),
        nameAr: nameAr.trim() || null,
        quote: quote.trim(),
        quoteAr: quoteAr.trim() || null,
        role: role.trim(),
        roleAr: roleAr.trim() || null,
        imagePath: imagePath ?? "",
        isActive,
      };
      if (initial) {
        await apiFetch(`/testimonials/${initial.id}`, {
          method: "PUT",
          body: JSON.stringify({ id: initial.id, ...body }),
        });
      } else {
        await apiFetch("/testimonials", { method: "POST", body: JSON.stringify(body) });
      }
      router.push("/admin/testimonials");
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
        href="/admin/testimonials"
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
          placeholder={t("testimonialNamePlaceholder")}
        />

        <Input
          label={t("nameAr")}
          name="nameAr"
          value={nameAr}
          onChange={(event) => setNameAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <TextArea
          label={t("quote")}
          name="quote"
          rows={4}
          value={quote}
          onChange={(event) => setQuote(event.target.value)}
          placeholder={t("quotePlaceholder")}
        />

        <TextArea
          label={t("quoteAr")}
          name="quoteAr"
          rows={4}
          value={quoteAr}
          onChange={(event) => setQuoteAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <Input
          label={t("role")}
          name="role"
          value={role}
          onChange={(event) => setRole(event.target.value)}
          placeholder={t("rolePlaceholder")}
        />

        <Input
          label={t("roleAr")}
          name="roleAr"
          value={roleAr}
          onChange={(event) => setRoleAr(event.target.value)}
          placeholder={t("arPlaceholder")}
        />

        <ImageUpload entity="testimonials" label={t("photo")} value={imagePath} onChange={setImagePath} />

        <Toggle
          checked={isActive}
          onChange={setIsActive}
          label={t("active")}
          description={t("inactiveHidden", { entity: section("entity") })}
        />

        {error && <p className="border-s-2 border-tertiary bg-neutral-light px-3 py-2 text-sm text-tertiary">{error}</p>}

        <div className="flex justify-end gap-2 border-t border-line pt-5">
          <Link href="/admin/testimonials">
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
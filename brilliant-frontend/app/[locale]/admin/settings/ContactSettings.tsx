"use client";

import { useCallback, useEffect, useState } from "react";
import { useTranslations } from "next-intl";
import { apiFetch } from "@/lib/api";
import type { ContactInfo } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";

const EMPTY_FORM = {
  phone1: "",
  phone2: "",
  email: "",
  email2: "",
  address: "",
  addressAr: "",
  mapEmbedUrl: "",
};

export function ContactSettings() {
  const t = useTranslations("admin.settings.contact");
  const settings = useTranslations("admin.settings");
  const [form, setForm] = useState(EMPTY_FORM);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState<{ type: "error" | "success"; text: string } | null>(null);

  const load = useCallback(async () => {
    try {
      const data = await apiFetch<ContactInfo>("/contact");
      setForm({
        phone1: data.phone1 ?? "",
        phone2: data.phone2 ?? "",
        email: data.email ?? "",
        email2: data.email2 ?? "",
        address: data.address ?? "",
        addressAr: data.addressAr ?? "",
        mapEmbedUrl: data.mapEmbedUrl ?? "",
      });
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("loadFailed") });
    } finally {
      setLoading(false);
    }
  }, [t]);

  useEffect(() => {
    load();
  }, [load]);

  function updateField<K extends keyof typeof EMPTY_FORM>(key: K, value: string) {
    setForm((prev) => ({ ...prev, [key]: value }));
  }

  function normalizeMapUrl(url: string): string {
    if (!url || /\/maps\/embed/i.test(url)) return url;
    const cid = url.match(/[?&]cid=(\d+)/i)?.[1];
    if (cid) return `https://www.google.com/maps/embed?pb=!1m3!3m2!1m1!4s${cid}`;
    const q = url.match(/[?&]q=([^&#]+)/i)?.[1];
    if (q) {
      const encoded = encodeURIComponent(decodeURIComponent(q)).replace(/%20/g, "+");
      return `https://www.google.com/maps/embed?pb=!1m3!2m1!1s${encoded}!6i14`;
    }
    return url;
  }

  async function save(event: React.FormEvent) {
    event.preventDefault();
    setSaving(true);
    setMessage(null);
    try {
      await apiFetch("/contact", { method: "PUT", body: JSON.stringify(form) });
      setMessage({ type: "success", text: t("saved") });
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("saveFailed") });
    } finally {
      setSaving(false);
    }
  }

  return (
    <section className="card-brilliant space-y-6 bg-white p-6">
      <div>
        <h2 className="font-headline text-lg font-bold uppercase">{t("title")}</h2>
        <p className="mt-1 text-sm text-neutral/60">{t("description")}</p>
      </div>

      {loading ? (
        <p className="py-8 text-center text-sm text-neutral/50">{settings("loading")}</p>
      ) : (
        <form onSubmit={save} className="space-y-5">
          <div className="grid gap-5 sm:grid-cols-2">
            <Input
              label={t("primaryPhone")}
              name="phone1"
              value={form.phone1}
              onChange={(event) => updateField("phone1", event.target.value)}
            />
            <Input
              label={t("secondaryPhone")}
              name="phone2"
              value={form.phone2}
              onChange={(event) => updateField("phone2", event.target.value)}
            />
          </div>
          <Input
            label={t("email")}
            name="email"
            type="email"
            value={form.email}
            onChange={(event) => updateField("email", event.target.value)}
          />
          <Input
            label={t("secondaryEmail")}
            name="email2"
            type="email"
            value={form.email2}
            onChange={(event) => updateField("email2", event.target.value)}
          />
          <TextArea
            label={t("address")}
            name="address"
            rows={2}
            value={form.address}
            onChange={(event) => updateField("address", event.target.value)}
          />
          <TextArea
            label={t("addressAr")}
            name="addressAr"
            rows={2}
            value={form.addressAr}
            onChange={(event) => updateField("addressAr", event.target.value)}
          />
          <Input
            label={t("mapEmbedUrl")}
            name="mapEmbedUrl"
            value={form.mapEmbedUrl}
            onChange={(event) => updateField("mapEmbedUrl", normalizeMapUrl(event.target.value))}
            placeholder="https://www.google.com/maps/embed?pb=â€¦"
          />
          <p className="-mt-2 text-xs text-neutral/60">
            {t("mapHint")}
          </p>

          {message && (
            <p
              className={
                message.type === "error"
                  ? "border-s-2 border-tertiary bg-neutral-light px-3 py-2 text-sm text-tertiary"
                  : "border-s-2 border-green-600 bg-green-50 px-3 py-2 text-sm text-green-700"
              }
            >
              {message.text}
            </p>
          )}

          <div className="flex justify-end">
            <Button type="submit" loading={saving}>
              {saving ? settings("saving") : t("save")}
            </Button>
          </div>
        </form>
      )}
    </section>
  );
}

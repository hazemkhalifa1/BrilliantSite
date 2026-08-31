"use client";

import { useCallback, useEffect, useState } from "react";
import { useTranslations } from "next-intl";
import { Trash2 } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { HeroSection, HeroStat } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { TextArea } from "@/components/admin/TextArea";
import { Toggle } from "@/components/admin/Toggle";
import { AdminTable, type Column } from "@/components/admin/AdminTable";
import { Badge } from "@/components/ui/Badge";

const EMPTY_FORM = {
  headlineTop: "",
  headlineTopAr: "",
  headlineBottom: "",
  headlineBottomAr: "",
  subText: "",
  subTextAr: "",
  primaryBtnText: "",
  primaryBtnTextAr: "",
  primaryBtnUrl: "",
  secondaryBtnText: "",
  secondaryBtnTextAr: "",
  secondaryBtnUrl: "",
};

export function HeroSettings() {
  const t = useTranslations("admin.settings.hero");
  const settings = useTranslations("admin.settings");
  const common = useTranslations("common");
  const [form, setForm] = useState(EMPTY_FORM);
  const [stats, setStats] = useState<HeroStat[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState<{ type: "error" | "success"; text: string } | null>(null);

  const [newValue, setNewValue] = useState("");
  const [newLabel, setNewLabel] = useState("");
  const [newLabelAr, setNewLabelAr] = useState("");
  const [newActive, setNewActive] = useState(true);
  const [adding, setAdding] = useState(false);

  const load = useCallback(async () => {
    try {
      const [heroData, statsData] = await Promise.all([
        apiFetch<HeroSection>("/hero"),
        apiFetch<{ items: HeroStat[]; totalCount: number }>("/hero/stats?pageSize=100&onlyActive=false"),
      ]);
      setForm({
        headlineTop: heroData.headlineTop ?? "",
        headlineTopAr: heroData.headlineTopAr ?? "",
        headlineBottom: heroData.headlineBottom ?? "",
        headlineBottomAr: heroData.headlineBottomAr ?? "",
        subText: heroData.subText ?? "",
        subTextAr: heroData.subTextAr ?? "",
        primaryBtnText: heroData.primaryBtnText ?? "",
        primaryBtnTextAr: heroData.primaryBtnTextAr ?? "",
        primaryBtnUrl: heroData.primaryBtnUrl ?? "",
        secondaryBtnText: heroData.secondaryBtnText ?? "",
        secondaryBtnTextAr: heroData.secondaryBtnTextAr ?? "",
        secondaryBtnUrl: heroData.secondaryBtnUrl ?? "",
      });
      setStats(statsData.items);
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

  async function saveHero(event: React.FormEvent) {
    event.preventDefault();
    setSaving(true);
    setMessage(null);
    try {
      await apiFetch("/hero", { method: "PUT", body: JSON.stringify(form) });
      setMessage({ type: "success", text: t("saved") });
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("saveFailed") });
    } finally {
      setSaving(false);
    }
  }

  async function addStat(event: React.FormEvent) {
    event.preventDefault();
    if (!newValue.trim() || !newLabel.trim()) {
      setMessage({ type: "error", text: t("statRequired") });
      return;
    }
    setAdding(true);
    setMessage(null);
    try {
      await apiFetch("/hero/stats", {
        method: "POST",
        body: JSON.stringify({ value: newValue.trim(), label: newLabel.trim(), labelAr: newLabelAr.trim() || null, isActive: newActive }),
      });
      setNewValue("");
      setNewLabel("");
      setNewLabelAr("");
      setNewActive(true);
      load();
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("addFailed") });
    } finally {
      setAdding(false);
    }
  }

  async function deleteStat(id: number) {
    try {
      await apiFetch(`/hero/stats/${id}`, { method: "DELETE" });
      load();
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("deleteFailed") });
    }
  }

  const columns: Column<HeroStat>[] = [
    {
      key: "value",
      header: t("value"),
      cell: (stat) => <span className="font-semibold text-neutral">{stat.value}</span>,
    },
    { key: "label", header: t("label"), cell: (stat) => stat.label || "â€”" },
    { key: "order", header: common("order"), cell: (stat) => stat.order },
    {
      key: "status",
      header: common("status"),
      cell: (stat) => (
        <Badge variant={stat.isActive ? "success" : "neutral"}>
          {stat.isActive ? common("active") : common("inactive")}
        </Badge>
      ),
    },
  ];

  if (loading) {
    return (
      <div className="card-brilliant flex h-64 items-center justify-center bg-white text-sm text-neutral/50">
        {settings("loading")}
      </div>
    );
  }

  return (
    <section className="card-brilliant space-y-8 bg-white p-6">
      <div>
        <h2 className="font-headline text-lg font-bold uppercase">{t("title")}</h2>
        <p className="mt-1 text-sm text-neutral/60">{t("description")}</p>
      </div>

      <form onSubmit={saveHero} className="space-y-5">
        <div className="grid gap-5 sm:grid-cols-2">
          <Input
            label={t("headlineTop")}
            name="headlineTop"
            value={form.headlineTop}
            onChange={(event) => updateField("headlineTop", event.target.value)}
            placeholder={t("headlineTopPlaceholder")}
          />
          <Input
            label={t("headlineTopAr")}
            name="headlineTopAr"
            value={form.headlineTopAr}
            onChange={(event) => updateField("headlineTopAr", event.target.value)}
            placeholder={t("arPlaceholder")}
          />
          <Input
            label={t("headlineBottom")}
            name="headlineBottom"
            value={form.headlineBottom}
            onChange={(event) => updateField("headlineBottom", event.target.value)}
            placeholder={t("headlineBottomPlaceholder")}
          />
          <Input
            label={t("headlineBottomAr")}
            name="headlineBottomAr"
            value={form.headlineBottomAr}
            onChange={(event) => updateField("headlineBottomAr", event.target.value)}
            placeholder={t("arPlaceholder")}
          />
        </div>

        <TextArea
          label={t("subText")}
          name="subText"
          rows={3}
          value={form.subText}
          onChange={(event) => updateField("subText", event.target.value)}
        />
        <TextArea
          label={t("subTextAr")}
          name="subTextAr"
          rows={3}
          value={form.subTextAr}
          onChange={(event) => updateField("subTextAr", event.target.value)}
        />

        <div className="grid gap-5 sm:grid-cols-2">
          <Input
            label={t("primaryBtnText")}
            name="primaryBtnText"
            value={form.primaryBtnText}
            onChange={(event) => updateField("primaryBtnText", event.target.value)}
          />
          <Input
            label={t("primaryBtnTextAr")}
            name="primaryBtnTextAr"
            value={form.primaryBtnTextAr}
            onChange={(event) => updateField("primaryBtnTextAr", event.target.value)}
            placeholder={t("arPlaceholder")}
          />
          <Input
            label={t("primaryBtnUrl")}
            name="primaryBtnUrl"
            value={form.primaryBtnUrl}
            onChange={(event) => updateField("primaryBtnUrl", event.target.value)}
            placeholder="/contact"
          />
          <Input
            label={t("secondaryBtnText")}
            name="secondaryBtnText"
            value={form.secondaryBtnText}
            onChange={(event) => updateField("secondaryBtnText", event.target.value)}
          />
          <Input
            label={t("secondaryBtnTextAr")}
            name="secondaryBtnTextAr"
            value={form.secondaryBtnTextAr}
            onChange={(event) => updateField("secondaryBtnTextAr", event.target.value)}
            placeholder={t("arPlaceholder")}
          />
          <Input
            label={t("secondaryBtnUrl")}
            name="secondaryBtnUrl"
            value={form.secondaryBtnUrl}
            onChange={(event) => updateField("secondaryBtnUrl", event.target.value)}
            placeholder="/services"
          />
        </div>

        <div className="flex justify-end">
          <Button type="submit" loading={saving}>
            {saving ? settings("saving") : t("save")}
          </Button>
        </div>
      </form>

      <div className="border-t border-line pt-8">
        <h3 className="font-headline text-base font-bold uppercase">{t("stats")}</h3>
        <p className="mt-1 text-sm text-neutral/60">{t("statsDescription")}</p>

        <div className="mt-5">
          <AdminTable
            columns={columns}
            data={stats}
            rowKey={(stat) => stat.id}
            emptyMessage={t("noStats")}
            actions={(stat) => (
              <div className="flex justify-end">
                <button
                  type="button"
                  onClick={() => deleteStat(stat.id)}
                  aria-label={t("deleteStat")}
                  className="inline-flex h-8 w-8 items-center justify-center border border-line text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>
            )}
          />
        </div>

        <form onSubmit={addStat} className="mt-5 grid gap-4 border border-line bg-neutral-light p-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
          <Input
            label={t("value")}
            name="statValue"
            value={newValue}
            onChange={(event) => setNewValue(event.target.value)}
            placeholder={t("statValuePlaceholder")}
          />
          <Input
            label={t("label")}
            name="statLabel"
            value={newLabel}
            onChange={(event) => setNewLabel(event.target.value)}
            placeholder={t("statLabelPlaceholder")}
          />
          <Input
            label={t("labelAr")}
            name="statLabelAr"
            value={newLabelAr}
            onChange={(event) => setNewLabelAr(event.target.value)}
            placeholder={t("arPlaceholder")}
          />
          <div className="pb-1">
            <Toggle checked={newActive} onChange={setNewActive} label={common("active")} />
          </div>
          <Button type="submit" variant="secondary" loading={adding} className="sm:col-span-2">
            {adding ? t("adding") : t("add")}
          </Button>
        </form>
      </div>

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
    </section>
  );
}

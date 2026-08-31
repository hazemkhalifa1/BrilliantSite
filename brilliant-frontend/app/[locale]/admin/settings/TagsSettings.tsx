"use client";

import { useCallback, useEffect, useState } from "react";
import { useTranslations } from "next-intl";
import { Trash2 } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { Tag } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";

export function TagsSettings() {
  const t = useTranslations("admin.settings.tags");
  const settings = useTranslations("admin.settings");
  const [tags, setTags] = useState<Tag[]>([]);
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState<{ type: "error" | "success"; text: string } | null>(null);

  const [name, setName] = useState("");
  const [adding, setAdding] = useState(false);

  const load = useCallback(async () => {
    try {
      const data = await apiFetch<{ items: Tag[]; totalCount: number }>("/tags?pageSize=100");
      setTags(data.items);
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("loadFailed") });
    } finally {
      setLoading(false);
    }
  }, [t]);

  useEffect(() => {
    load();
  }, [load]);

  async function addTag(event: React.FormEvent) {
    event.preventDefault();
    if (!name.trim()) {
      setMessage({ type: "error", text: t("required") });
      return;
    }
    setAdding(true);
    setMessage(null);
    try {
      await apiFetch("/tags", { method: "POST", body: JSON.stringify({ name: name.trim() }) });
      setName("");
      load();
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("addFailed") });
    } finally {
      setAdding(false);
    }
  }

  async function deleteTag(id: number) {
    try {
      await apiFetch(`/tags/${id}`, { method: "DELETE" });
      load();
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("deleteFailed") });
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
        <>
          {tags.length > 0 ? (
            <ul className="divide-y divide-line border border-line">
              {tags.map((tag) => (
                <li key={tag.id} className="flex items-center justify-between gap-3 px-4 py-3">
                  <div>
                    <span className="font-semibold text-neutral">{tag.name}</span>
                    <p className="text-xs text-neutral/50">/{tag.slug}</p>
                  </div>
                  <button
                    type="button"
                    onClick={() => deleteTag(tag.id)}
                    aria-label={t("delete", { name: tag.name })}
                    className="inline-flex h-8 w-8 items-center justify-center border border-line text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"
                  >
                    <Trash2 className="h-4 w-4" />
                  </button>
                </li>
              ))}
            </ul>
          ) : (
            <p className="py-6 text-center text-sm text-neutral/50">{t("empty")}</p>
          )}

          <form onSubmit={addTag} className="flex flex-col gap-3 sm:flex-row sm:items-end">
            <Input
              label={t("tagName")}
              name="tagName"
              value={name}
              onChange={(event) => setName(event.target.value)}
              placeholder={t("tagNamePlaceholder")}
            />
            <Button type="submit" variant="secondary" loading={adding}>
              {adding ? t("adding") : t("add")}
            </Button>
          </form>
        </>
      )}

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

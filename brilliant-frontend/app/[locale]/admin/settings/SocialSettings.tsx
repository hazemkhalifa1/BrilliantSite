"use client";

import { useCallback, useEffect, useState } from "react";
import { useTranslations } from "next-intl";
import { Trash2 } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { SocialLink } from "@/types";
import { Input } from "@/components/ui/Input";
import { Button } from "@/components/ui/Button";
import { Toggle } from "@/components/admin/Toggle";
import { Badge } from "@/components/ui/Badge";

export function SocialSettings() {
  const t = useTranslations("admin.settings.social");
  const settings = useTranslations("admin.settings");
  const common = useTranslations("common");
  const [links, setLinks] = useState<SocialLink[]>([]);
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState<{ type: "error" | "success"; text: string } | null>(null);

  const [platform, setPlatform] = useState("");
  const [url, setUrl] = useState("");
  const [iconClass, setIconClass] = useState("");
  const [isActive, setIsActive] = useState(true);
  const [adding, setAdding] = useState(false);

  const load = useCallback(async () => {
    try {
      const data = await apiFetch<{ items: SocialLink[]; totalCount: number }>(
        "/social-links?pageSize=100&onlyActive=false",
      );
      setLinks(data.items);
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("loadFailed") });
    } finally {
      setLoading(false);
    }
  }, [t]);

  useEffect(() => {
    load();
  }, [load]);

  async function addLink(event: React.FormEvent) {
    event.preventDefault();
    if (!platform.trim() || !url.trim()) {
      setMessage({ type: "error", text: t("required") });
      return;
    }
    setAdding(true);
    setMessage(null);
    try {
      await apiFetch("/social-links", {
        method: "POST",
        body: JSON.stringify({ platform: platform.trim(), url: url.trim(), iconClass, isActive }),
      });
      setPlatform("");
      setUrl("");
      setIconClass("");
      setIsActive(true);
      load();
    } catch (err) {
      setMessage({ type: "error", text: err instanceof Error ? err.message : t("addFailed") });
    } finally {
      setAdding(false);
    }
  }

  async function deleteLink(id: number) {
    try {
      await apiFetch(`/social-links/${id}`, { method: "DELETE" });
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
          {links.length > 0 ? (
            <ul className="divide-y divide-line border border-line">
              {links.map((link) => (
                <li key={link.id} className="flex items-center justify-between gap-3 px-4 py-3">
                  <div className="min-w-0">
                    <div className="flex items-center gap-2">
                      <span className="font-semibold text-neutral">{link.platform}</span>
                      <Badge variant={link.isActive ? "success" : "neutral"}>
                        {link.isActive ? common("active") : common("inactive")}
                      </Badge>
                    </div>
                    <a
                      href={link.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="block truncate text-xs text-neutral/50 hover:text-secondary"
                    >
                      {link.url}
                    </a>
                  </div>
                  <button
                    type="button"
                    onClick={() => deleteLink(link.id)}
                    aria-label={t("delete", { platform: link.platform })}
                    className="inline-flex h-8 w-8 shrink-0 items-center justify-center border border-line text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"
                  >
                    <Trash2 className="h-4 w-4" />
                  </button>
                </li>
              ))}
            </ul>
          ) : (
            <p className="py-6 text-center text-sm text-neutral/50">{t("empty")}</p>
          )}

          <form onSubmit={addLink} className="space-y-4 border border-line bg-neutral-light p-4">
            <div className="grid gap-4 sm:grid-cols-2">
              <Input
                label={t("platform")}
                name="platform"
                value={platform}
                onChange={(event) => setPlatform(event.target.value)}
                placeholder={t("platformPlaceholder")}
              />
              <Input
                label={t("url")}
                name="url"
                type="url"
                value={url}
                onChange={(event) => setUrl(event.target.value)}
                placeholder="https://linkedin.com/â€¦"
              />
            </div>
            <Input
              label={t("iconClass")}
              name="iconClass"
              value={iconClass}
              onChange={(event) => setIconClass(event.target.value)}
              placeholder="fab fa-linkedin"
            />
            <div className="flex items-center justify-between gap-4">
              <Toggle checked={isActive} onChange={setIsActive} label={common("active")} />
              <Button type="submit" variant="secondary" loading={adding}>
                {adding ? t("adding") : t("add")}
              </Button>
            </div>
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

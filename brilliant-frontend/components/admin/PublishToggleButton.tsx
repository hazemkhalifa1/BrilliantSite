"use client";

import { useState } from "react";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { Eye, EyeOff, Loader2 } from "lucide-react";
import { apiFetch } from "@/lib/api";

export interface PublishToggleButtonProps {
  id: number;
  isPublished: boolean;
}

export function PublishToggleButton({ id, isPublished }: PublishToggleButtonProps) {
  const t = useTranslations("admin.publish");
  const router = useRouter();
  const [loading, setLoading] = useState(false);

  async function toggle() {
    setLoading(true);
    try {
      await apiFetch(`/blog/${id}/${isPublished ? "unpublish" : "publish"}`, { method: "POST" });
      if (!isPublished) {
        fetch("/api/seo/ping", { method: "POST", keepalive: true }).catch(() => {});
      }
      router.refresh();
    } catch {
      // Ignore: list will refresh on next navigation
    } finally {
      setLoading(false);
    }
  }

  return (
    <button
      type="button"
      onClick={toggle}
      disabled={loading}
      className="inline-flex h-8 items-center gap-1.5 border border-line px-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:border-secondary hover:text-secondary disabled:cursor-not-allowed disabled:opacity-50"
    >
      {loading ? (
        <Loader2 className="h-3.5 w-3.5 animate-spin" />
      ) : isPublished ? (
        <EyeOff className="h-3.5 w-3.5" />
      ) : (
        <Eye className="h-3.5 w-3.5" />
      )}
      {isPublished ? t("unpublish") : t("publish")}
    </button>
  );
}

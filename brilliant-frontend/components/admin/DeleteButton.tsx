"use client";

import { useState } from "react";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { Trash2 } from "lucide-react";
import { apiFetch } from "@/lib/api";
import { ConfirmDialog } from "@/components/admin/ConfirmDialog";

export interface DeleteButtonProps {
  endpoint: string;
  title?: string;
  message?: string;
}

export function DeleteButton({
  endpoint,
  title,
  message,
}: DeleteButtonProps) {
  const t = useTranslations("admin.deleteButton");
  const router = useRouter();
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleDelete() {
    setLoading(true);
    setError(null);
    try {
      await apiFetch(endpoint, { method: "DELETE" });
      setOpen(false);
      router.refresh();
    } catch (err) {
      setError(err instanceof Error ? err.message : t("failed"));
    } finally {
      setLoading(false);
    }
  }

  return (
    <>
      <button
        type="button"
        onClick={() => setOpen(true)}
        aria-label={t("aria")}
        title={t("aria")}
        className="inline-flex h-8 w-8 items-center justify-center border border-line text-neutral/60 transition-colors hover:border-tertiary hover:bg-tertiary hover:text-white"
      >
        <Trash2 className="h-4 w-4" />
      </button>
      <ConfirmDialog
        open={open}
        title={title || t("title")}
        message={error || message || t("message")}
        loading={loading}
        onConfirm={handleDelete}
        onClose={() => setOpen(false)}
      />
    </>
  );
}

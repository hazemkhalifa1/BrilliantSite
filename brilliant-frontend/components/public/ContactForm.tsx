"use client";

import * as React from "react";
import { useTranslations } from "next-intl";
import { CheckCircle2, AlertCircle, Loader2 } from "lucide-react";
import { apiFetch } from "@/lib/api";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";

type Status = "idle" | "submitting" | "success" | "error";

export function ContactForm() {
  const t = useTranslations("contactForm");
  const [form, setForm] = React.useState({
    name: "",
    email: "",
    phone: "",
    message: "",
  });
  const [status, setStatus] = React.useState<Status>("idle");
  const [error, setError] = React.useState("");

  const update = (field: keyof typeof form) => (
    event: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ) => {
    setForm((current) => ({ ...current, [field]: event.target.value }));
    setStatus("idle");
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    if (!form.name || !form.email || !form.message) {
      setError(t("validationError"));
      setStatus("error");
      return;
    }
    setStatus("submitting");
    setError("");
    try {
      await apiFetch("/contact/messages", {
        method: "POST",
        body: JSON.stringify(form),
      });
      setStatus("success");
      setForm({ name: "", email: "", phone: "", message: "" });
    } catch (err) {
      setError(err instanceof Error ? err.message : t("genericError"));
      setStatus("error");
    }
  };

  return (
    <form onSubmit={handleSubmit} className="card-brilliant p-6 md:p-8">
      <h2 className="font-headline text-xl font-bold uppercase">{t("sendMessage")}</h2>
      <span className="mt-2 block h-[3px] w-10 bg-tertiary" aria-hidden="true" />

      <div className="mt-6 grid gap-4 sm:grid-cols-2">
        <Input
          label={t("fullName")}
          name="name"
          value={form.name}
          onChange={update("name")}
          placeholder={t("namePlaceholder")}
          required
        />
        <Input
          label={t("email")}
          type="email"
          name="email"
          value={form.email}
          onChange={update("email")}
          placeholder={t("emailPlaceholder")}
          required
        />
      </div>
      <div className="mt-4">
        <Input
          label={t("phone")}
          name="phone"
          value={form.phone}
          onChange={update("phone")}
          placeholder={t("phonePlaceholder")}
        />
      </div>
      <div className="mt-4 flex flex-col gap-1.5">
        <label htmlFor="message" className="text-sm font-medium text-neutral">
          {t("message")}
        </label>
        <textarea
          id="message"
          name="message"
          rows={5}
          value={form.message}
          onChange={update("message")}
          placeholder={t("messagePlaceholder")}
          required
          className="w-full border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none"
        />
      </div>

      {status === "success" && (
        <p className="mt-4 flex items-center gap-2 bg-green-50 px-3 py-2.5 text-sm text-green-700">
          <CheckCircle2 className="h-4 w-4 shrink-0" />
          {t("success")}
        </p>
      )}
      {status === "error" && (
        <p className="mt-4 flex items-center gap-2 bg-red-50 px-3 py-2.5 text-sm text-tertiary">
          <AlertCircle className="h-4 w-4 shrink-0" />
          {error}
        </p>
      )}

      <Button type="submit" variant="cta" loading={status === "submitting"} className="mt-6">
        {status === "submitting" ? (
          <><Loader2 className="h-4 w-4 animate-spin" /> {t("sending")}</>
        ) : (
          t("sendButton")
        )}
      </Button>
    </form>
  );
}

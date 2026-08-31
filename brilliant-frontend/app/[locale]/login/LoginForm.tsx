"use client";

import { useState } from "react";
import { useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { Eye, EyeOff } from "lucide-react";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";

export function LoginForm() {
  const t = useTranslations("login");
  const router = useRouter();
  const { signIn } = useAuth();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  function setTokenCookie(token: string) {
    document.cookie = `token=${token}; path=/; max-age=604800; SameSite=Lax`;
  }

  function clearTokenCookie() {
    document.cookie = "token=; path=/; max-age=0";
  }

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault();
    setError(null);

    if (!email.trim() || !password) {
      setError(t("validationError"));
      return;
    }

    setSubmitting(true);
    try {
      const response = await signIn({ email: email.trim(), password });
      setTokenCookie(response.token);
      router.push("/admin");
      router.refresh();
    } catch (err) {
      clearTokenCookie();
      setError(
        err instanceof Error && err.message
          ? err.message
          : t("signInFailed"),
      );
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-5">
      <Input
        label={t("email")}
        name="email"
        type="email"
        autoComplete="email"
        placeholder={t("emailPlaceholder")}
        value={email}
        onChange={(event) => setEmail(event.target.value)}
        error={error && email.trim() === "" ? t("emailRequired") : undefined}
      />

      <div>
        <label htmlFor="password" className="mb-1.5 block text-sm font-medium text-neutral">
          {t("password")}
        </label>
        <div className="relative">
          <Input
            name="password"
            id="password"
            type={showPassword ? "text" : "password"}
            autoComplete="current-password"
            placeholder="••••••••"
            value={password}
            onChange={(event) => setPassword(event.target.value)}
            error={error && password === "" ? t("passwordRequired") : undefined}
            className="pr-10"
          />
          <button
            type="button"
            onClick={() => setShowPassword((value) => !value)}
            aria-label={showPassword ? t("hidePassword") : t("showPassword")}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-neutral/50 transition-colors hover:text-neutral"
          >
            {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
          </button>
        </div>
      </div>

      {error && (
        <p className="border-l-2 border-tertiary bg-neutral-light px-3 py-2 text-sm text-tertiary">
          {error}
        </p>
      )}

      <Button type="submit" className="w-full" loading={submitting}>
        {submitting ? t("signingIn") : t("signIn")}
      </Button>
    </form>
  );
}

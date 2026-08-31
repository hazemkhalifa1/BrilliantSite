import type { Metadata } from "next";
import Image from "next/image";
import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import { Lock } from "lucide-react";
import { LoginForm } from "./LoginForm";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "login" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
    robots: { index: false, follow: false },
  };
}

export default async function LoginPage() {
  const t = await getTranslations("login");
  return (
    <main className="flex min-h-screen items-center justify-center bg-neutral-light px-4 py-12">
      <div className="w-full max-w-md">
        <div className="card-brilliant bg-white p-8 md:p-10">
          <div className="flex flex-col items-center text-center">
            <div className="flex h-12 w-12 items-center justify-center bg-primary text-white">
              <Lock className="h-5 w-5" />
            </div>
            <div className="relative mt-6 h-10 w-56">
              <Image
                src="/logo.png"
                alt="Brilliant Engineering"
                fill
                sizes="224px"
                className="object-contain"
              />
            </div>
            <h1 className="mt-6 font-headline text-xl font-bold uppercase">{t("title")}</h1>
            <p className="mt-1 text-sm text-neutral/60">
              {t("description")}
            </p>
          </div>

          <div className="mt-8">
            <LoginForm />
          </div>

          <p className="mt-6 text-center text-xs text-neutral/50">
            <Link href="/" className="transition-colors hover:text-secondary">
              {t("backToWebsite")}
            </Link>
          </p>
        </div>
      </div>
    </main>
  );
}

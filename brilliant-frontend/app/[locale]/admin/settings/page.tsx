import type { Metadata } from "next";
import { getTranslations } from "next-intl/server";
import { AdminPageHeader } from "@/components/admin/AdminPageHeader";
import { HeroSettings } from "./HeroSettings";
import { ContactSettings } from "./ContactSettings";
import { SocialSettings } from "./SocialSettings";
import { TagsSettings } from "./TagsSettings";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "admin.settings" });
  return { title: t("title") };
}

export default async function SettingsPage() {
  const t = await getTranslations("admin.settings");
  return (
    <div className="space-y-8">
      <AdminPageHeader
        title={t("title")}
        description={t("description")}
      />
      <div className="grid items-start gap-8 xl:grid-cols-2">
        <HeroSettings />
        <div className="space-y-8">
          <ContactSettings />
          <SocialSettings />
          <TagsSettings />
        </div>
      </div>
    </div>
  );
}

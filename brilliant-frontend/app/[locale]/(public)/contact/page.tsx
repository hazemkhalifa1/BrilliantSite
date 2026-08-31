import type { Metadata } from "next";
import { getLocale, getTranslations } from "next-intl/server";
import { MapPin, Phone, Mail } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { ContactInfo, PagedResult, SocialLink } from "@/types";
import { localized } from "@/lib/localize";
import { PageHeader } from "@/components/public/PageHeader";
import { ContactForm } from "@/components/public/ContactForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "contact" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
  };
}

async function getContactData() {
  const contact = await apiFetch<ContactInfo>("/contact").catch(() => null);
  const socials = await apiFetch<PagedResult<SocialLink>>("/social-links?onlyActive=true&pageSize=50")
    .then((data) => data.items)
    .catch(() => [] as SocialLink[]);
  return { contact, socials };
}

export default async function ContactPage() {
  const t = await getTranslations("contact");
  const locale = await getLocale();
  const { contact, socials } = await getContactData();

  return (
    <div>
      <PageHeader
        eyebrow={t("eyebrow")}
        title={t("title")}
        subtitle={t("subtitle")}
        backgroundImage="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80"
      />

      <section className="py-16 md:py-20">
        <div className="container-brilliant grid gap-10 lg:grid-cols-2">
          <ContactForm />

          <div>
            <div className="card-brilliant bg-neutral p-8 text-white">
              <h2 className="font-headline text-xl font-bold uppercase">{t("contactInfo")}</h2>
              <span className="mt-2 block h-[3px] w-10 bg-tertiary" aria-hidden="true" />

              <ul className="mt-6 space-y-5">
                <li className="flex items-start gap-4">
                  <Phone className="mt-0.5 h-5 w-5 shrink-0 text-tertiary" />
                  <div>
                    <p className="text-sm text-white/60">{t("phone")}</p>
                    <p className="mt-0.5">{contact?.phone1 || "—"}</p>
                    {contact?.phone2 && <p className="mt-0.5">{contact.phone2}</p>}
                  </div>
                </li>
                <li className="flex items-start gap-4">
                  <Mail className="mt-0.5 h-5 w-5 shrink-0 text-tertiary" />
                  <div>
                    <p className="text-sm text-white/60">{t("email")}</p>
                    <a
                      href={`mailto:${contact?.email || ""}`}
                      className="mt-0.5 block transition-colors hover:text-secondary"
                    >
                      {contact?.email || "—"}
                    </a>
                    {contact?.email2 && (
                      <a
                        href={`mailto:${contact.email2}`}
                        className="mt-0.5 block transition-colors hover:text-secondary"
                      >
                        {contact.email2}
                      </a>
                    )}
                  </div>
                </li>
                <li className="flex items-start gap-4">
                  <MapPin className="mt-0.5 h-5 w-5 shrink-0 text-tertiary" />
                  <div>
                    <p className="text-sm text-white/60">{t("address")}</p>
                    <p className="mt-0.5">{localized(locale, contact?.address ?? "", contact?.addressAr) || "—"}</p>
                  </div>
                </li>
              </ul>

              {socials.length > 0 && (
                <div className="mt-8 border-t border-white/15 pt-6">
                  <p className="font-headline text-sm font-semibold uppercase tracking-widest text-white/60">
                    {t("followUs")}
                  </p>
                  <div className="mt-3 flex flex-wrap gap-2">
                    {socials.map((social) => (
                      <a
                        key={social.id}
                        href={social.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="border border-white/25 px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-white transition-colors hover:border-tertiary hover:text-tertiary"
                      >
                        {social.platform}
                      </a>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </section>

      {contact?.mapEmbedUrl && (
        <section className="pb-16 md:pb-20">
          <div className="container-brilliant">
            <div className="aspect-[21/9] w-full overflow-hidden border border-line">
              <iframe
                src={contact.mapEmbedUrl}
                title={t("mapTitle")}
                className="h-full w-full"
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
              />
            </div>
          </div>
        </section>
      )}
    </div>
  );
}

import type { Metadata } from "next";
import { getLocale, getTranslations } from "next-intl/server";
import { localeAlternates } from "@/lib/seo";
import { MapPin, Phone, Mail } from "lucide-react";
import { apiFetch } from "@/lib/api";
import type { ContactInfo, PagedResult, SocialLink } from "@/types";
import { localized } from "@/lib/localize";
import { ContactForm } from "@/components/public/ContactForm";
import { Reveal } from "@/components/public/Reveal";
import { Parallax } from "@/components/public/Parallax";

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
    alternates: localeAlternates(locale, "/contact"),
  };
}

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";

const heroGridStyle = {
  backgroundImage:
    "linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px)",
  backgroundSize: "32px 32px",
} as const;

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
      <section className="relative overflow-hidden bg-neutral pb-24 pt-32 md:pb-32 md:pt-48">
        <div className="absolute inset-0" style={heroGridStyle} />
        <div className="absolute inset-0">
          <Parallax speed={0.08} className="h-full w-full">
            <div
              className="h-full w-full bg-cover bg-center opacity-30 mix-blend-luminosity"
              style={{ backgroundImage: `url('${HERO_IMAGE}')` }}
            />
          </Parallax>
        </div>
        <div className="absolute inset-0 bg-gradient-to-b from-neutral/80 to-neutral/50" />

        <div className="relative z-20 mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
          <span className="mb-6 inline-block bg-tertiary px-4 py-1.5 font-headline text-sm font-bold uppercase tracking-widest text-white">
            {t("eyebrow")}
          </span>
          <h1 className="mx-auto mb-6 max-w-3xl font-headline text-5xl font-bold uppercase leading-tight tracking-tighter text-white md:text-7xl">
            {t("title")}
          </h1>
          <p className="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300 md:text-xl">
            {t("subtitle")}
          </p>
        </div>
      </section>

      <section className="py-16 md:py-20">
        <div className="container-brilliant grid gap-10 lg:grid-cols-2">
          <Reveal from="left">
            <ContactForm />
          </Reveal>

          <Reveal from="right" delay={120}>
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
          </Reveal>
        </div>
      </section>

      {contact?.mapEmbedUrl && (
        <section className="pb-16 md:pb-20">
          <div className="container-brilliant">
            <Reveal>
              <div className="aspect-[21/9] w-full overflow-hidden border border-line">
                <iframe
                  src={contact.mapEmbedUrl}
                  title={t("mapTitle")}
                  className="h-full w-full"
                  loading="lazy"
                  referrerPolicy="no-referrer-when-downgrade"
                />
              </div>
            </Reveal>
          </div>
        </section>
      )}
    </div>
  );
}

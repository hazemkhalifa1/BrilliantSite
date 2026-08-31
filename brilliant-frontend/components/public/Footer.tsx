import Image from "next/image";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { apiFetch } from "@/lib/api";
import type { ContactInfo } from "@/types";
import { localized } from "@/lib/localize";

async function getContact(): Promise<ContactInfo | null> {
  return apiFetch<ContactInfo>("/contact").catch(() => null);
}

export async function Footer() {
  const t = await getTranslations("footer");
  const tNav = await getTranslations("nav");
  const locale = await getLocale();
  const contact = await getContact();

  const QUICK_LINKS = [
    { label: tNav("home"), href: "/" },
    { label: tNav("about"), href: "/about" },
    { label: tNav("services"), href: "/services" },
    { label: tNav("projects"), href: "/projects" },
    { label: tNav("products"), href: "/products" },
    { label: tNav("blog"), href: "/blog" },
    { label: tNav("contact"), href: "/contact" },
  ];

  const RESOURCE_LINKS = [
    { label: t("team"), href: "/team" },
    { label: t("clients"), href: "/clients" },
  ];

  return (
    <footer className="border-t-4 border-[#BA1A1A] bg-[#111C2D] py-20">
      <div className="container-brilliant grid grid-cols-1 gap-8 md:grid-cols-4 lg:gap-12">
        <div className="flex max-w-sm flex-col items-start gap-8">
          <Image
            src="/logo.png"
            alt="Brilliant Engineering"
            width={168}
            height={48}
            className="h-16 w-auto"
          />
          <p className="text-white/60">
            {t("description")}
          </p>
        </div>

        <div className="space-y-6">
          <h4 className="font-headline text-xs font-bold uppercase tracking-[0.3em] text-[#BA1A1A]">
            {t("company")}
          </h4>
          <div className="flex flex-col gap-4">
            {QUICK_LINKS.slice(0, 4).map((link) => (
              <Link
                key={link.href}
                href={link.href}
                className="text-sm text-white/60 transition-colors hover:text-[#BA1A1A]"
              >
                {link.label}
              </Link>
            ))}
          </div>
        </div>

        <div className="space-y-6">
          <h4 className="font-headline text-xs font-bold uppercase tracking-[0.3em] text-[#BA1A1A]">
            {t("portal")}
          </h4>
          <div className="flex flex-col gap-4">
            {QUICK_LINKS.slice(4).map((link) => (
              <Link
                key={link.href}
                href={link.href}
                className="text-sm text-white/60 transition-colors hover:text-[#BA1A1A]"
              >
                {link.label}
              </Link>
            ))}
            {RESOURCE_LINKS.map((link) => (
              <Link
                key={link.href}
                href={link.href}
                className="text-sm text-white/60 transition-colors hover:text-[#BA1A1A]"
              >
                {link.label}
              </Link>
            ))}
          </div>
        </div>

        <div className="space-y-6">
          <h4 className="font-headline text-xs font-bold uppercase tracking-[0.3em] text-[#BA1A1A]">
            {t("contact")}
          </h4>
          <div className="flex flex-col gap-4 text-sm text-white/60">
            <p className="whitespace-pre-line leading-relaxed">
              {localized(locale, contact?.address ?? "", contact?.addressAr) || t("contactPrompt")}
            </p>
            {contact?.phone1 && (
              <a href={`tel:${contact.phone1}`} className="transition-colors hover:text-[#BA1A1A]">
                {contact.phone1}
              </a>
            )}
            {contact?.phone2 && (
              <a href={`tel:${contact.phone2}`} className="transition-colors hover:text-[#BA1A1A]">
                {contact.phone2}
              </a>
            )}
            {contact?.email && (
              <a
                href={`mailto:${contact.email}`}
                className="transition-colors hover:text-[#BA1A1A]"
              >
                {contact.email}
              </a>
            )}
            {contact?.email2 && (
              <a
                href={`mailto:${contact.email2}`}
                className="transition-colors hover:text-[#BA1A1A]"
              >
                {contact.email2}
              </a>
            )}
          </div>
        </div>
      </div>

      <div className="mt-20 border-t border-white/10">
        <div className="container-brilliant flex flex-col items-center justify-between gap-6 pt-10 md:flex-row">
          <div className="font-headline text-xs uppercase tracking-widest text-white/50">
            {t("rights", { year: new Date().getFullYear() })}
          </div>
          <div className="font-headline text-xs italic uppercase tracking-[0.5em] text-[#BA1A1A]">
            {t("tagline")}
          </div>
        </div>
      </div>
    </footer>
  );
}

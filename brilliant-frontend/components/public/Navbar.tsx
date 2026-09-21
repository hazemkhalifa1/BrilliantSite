"use client";

import * as React from "react";
import Image from "next/image";
import { Link } from "@/src/i18n/navigation";
import { usePathname } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import { Menu, X } from "lucide-react";
import { cn } from "@/lib/utils";
import { LanguageSwitcher } from "./LanguageSwitcher";

export function Navbar() {
  const t = useTranslations("nav");
  const pathname = usePathname();
  const [open, setOpen] = React.useState(false);
  const [scrolled, setScrolled] = React.useState(false);

  const NAV_LINKS = [
    { label: t("home"), href: "/" },
    { label: t("about"), href: "/about" },
    { label: t("services"), href: "/services" },
    { label: t("projects"), href: "/projects" },
    { label: t("products"), href: "/products" },
    { label: t("blog"), href: "/blog" },
    { label: t("contact"), href: "/contact" },
  ];

  React.useEffect(() => {
    setOpen(false);
  }, [pathname]);

  React.useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <header
      className={cn(
        "absolute left-0 right-0 top-0 z-50 transition-all duration-300",
        scrolled
          ? "border-b border-white/10 bg-[#111C2D]/95 shadow-lg shadow-black/30 backdrop-blur-sm"
          : "bg-transparent",
      )}
    >
      <div className="container-brilliant">
        <div className="flex h-20 w-full items-center justify-between">
        <Link href="/" className="flex items-center" aria-label={t("homeAria")}>
          <Image
            src="/logo.png"
            alt="Brilliant Engineering"
            width={144}
            height={44}
            priority
            className="h-12 w-auto"
          />
        </Link>

        <nav className="hidden items-center gap-10 lg:flex" aria-label={t("mainNav")}>
          {NAV_LINKS.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className={cn(
                "border-b-2 pb-1 font-headline text-sm uppercase tracking-widest transition-colors",
                pathname === link.href
                  ? "border-[#BA1A1A] font-bold text-[#BA1A1A]"
                  : "border-transparent text-white hover:text-[#BA1A1A]",
              )}
            >
              {link.label}
            </Link>
          ))}
          <LanguageSwitcher />
          <Link
            href="/contact"
            className="bg-[#BA1A1A] px-8 py-2.5 font-headline text-sm uppercase tracking-widest text-white transition-transform hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
          >
            {t("getQuote")}
          </Link>
        </nav>

        <button
          className="p-2 text-white lg:hidden"
          onClick={() => setOpen((value) => !value)}
          aria-label={t("toggleMenu")}
        >
          {open ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
        </button>
        </div>
      </div>

      {open && (
        <nav className="border-t border-white/10 bg-[#111C2D] lg:hidden" aria-label={t("mobileNav")}>
          <div className="flex w-full flex-col px-4 py-4 sm:px-6 lg:px-8">
            {NAV_LINKS.map((link) => (
              <Link
                key={link.href}
                href={link.href}
                className={cn(
                  "border-b border-white/10 py-3 font-headline text-sm uppercase tracking-widest transition-colors",
                  pathname === link.href ? "font-bold text-[#BA1A1A]" : "text-white hover:text-[#BA1A1A]",
                )}
              >
                {link.label}
              </Link>
            ))}
            <LanguageSwitcher className="mt-4 inline-block text-center" />
            <Link
              href="/contact"
              className="mt-4 bg-[#BA1A1A] px-8 py-3 text-center font-headline text-sm uppercase tracking-widest text-white transition-colors hover:opacity-90"
            >
              {t("getQuote")}
            </Link>
          </div>
        </nav>
      )}
    </header>
  );
}

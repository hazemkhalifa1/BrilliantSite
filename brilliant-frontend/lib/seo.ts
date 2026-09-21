import type { Metadata } from "next";

/**
 * Single source of truth for the site's canonical origin.
 * The preferred URL is the www + "/en/…" variant (see .htaccess / middleware
 * which 301 non-www and www to this host).
 */
export const SITE_URL =
  process.env.NEXT_PUBLIC_SITE_URL || "https://www.brilliant-eng.com";

export function canonicalUrl(locale: string, path = ""): string {
  return `${SITE_URL}/${locale}${path}`;
}

/**
 * Canonical + hreflang alternates for a locale-prefixed page.
 * Every public page should merge this into its generateMetadata so Google
 * sees one unambiguous preferred URL instead of duplicate variants.
 */
export function localeAlternates(
  locale: string,
  path = "",
): NonNullable<Metadata["alternates"]> {
  return {
    canonical: canonicalUrl(locale, path),
    languages: {
      en: `${SITE_URL}/en${path}`,
      ar: `${SITE_URL}/ar${path}`,
      "x-default": `${SITE_URL}/en${path}`,
    },
  };
}

/**
 * Mirrors the backend's blog slug canonicalization: legacy auto-appended
 * numeric suffixes ("…-2", "…-3") always resolve to the clean slug.
 */
export function canonicalSlug(slug: string): string {
  const clean = slug.replace(/-\d+$/, "");
  return clean !== "" ? clean : slug;
}
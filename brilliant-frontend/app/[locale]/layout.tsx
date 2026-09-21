import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { NextIntlClientProvider } from "next-intl";
import { getMessages, getTranslations } from "next-intl/server";
import { Cairo, Space_Grotesk } from "next/font/google";
import { routing } from "@/src/i18n/routing";
import { SITE_URL, canonicalUrl } from "@/lib/seo";
import "../globals.css";

const cairo = Cairo({
  subsets: ["arabic", "latin"],
  weight: ["400", "600", "700", "900"],
  variable: "--font-cairo",
  display: "swap",
});

const spaceGrotesk = Space_Grotesk({
  subsets: ["latin"],
  weight: ["400", "600", "700"],
  variable: "--font-space-grotesk",
  display: "swap",
});

const OG_IMAGE = `${SITE_URL}/logo.png`;

interface LocaleLayoutProps {
  children: React.ReactNode;
  params: { locale: string };
}

export async function generateMetadata({
  params: { locale },
}: LocaleLayoutProps): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "metadata" });

  return {
    metadataBase: new URL(SITE_URL),
    title: {
      default: t("title"),
      template: `%s | ${t("siteName")}`,
    },
    description: t("description"),
    keywords: [
      t("keywords.general"),
      t("keywords.turnkey"),
      t("keywords.mep"),
      t("keywords.civil"),
      t("keywords.hvac"),
      t("keywords.industrial"),
      t("keywords.company"),
    ],
    authors: [{ name: t("company") }],
    creator: t("company"),
    alternates: {
      canonical: canonicalUrl(locale),
      languages: {
        en: canonicalUrl("en"),
        ar: canonicalUrl("ar"),
        "x-default": canonicalUrl("en"),
      },
    },
    openGraph: {
      type: "website",
      locale: locale === "ar" ? "ar_EG" : "en_US",
      url: canonicalUrl(locale),
      siteName: t("siteName"),
      title: t("title"),
      description: t("ogDescription"),
      images: [
        {
          url: OG_IMAGE,
          width: 1200,
          height: 630,
          alt: t("company"),
        },
      ],
    },
    twitter: {
      card: "summary_large_image",
      title: t("title"),
      description: t("ogDescription"),
      images: [OG_IMAGE],
    },
    robots: {
      index: true,
      follow: true,
      googleBot: {
        index: true,
        follow: true,
      },
    },
  };
}

export default async function LocaleLayout({
  children,
  params: { locale },
}: LocaleLayoutProps) {
  if (!routing.locales.includes(locale as (typeof routing.locales)[number])) {
    notFound();
  }

  const messages = await getMessages();

  return (
    <html
      lang={locale}
      dir={locale === "ar" ? "rtl" : "ltr"}
      className={`${cairo.variable} ${spaceGrotesk.variable}`}
    >
      <body>
        <NextIntlClientProvider messages={messages}>{children}</NextIntlClientProvider>
      </body>
    </html>
  );
}

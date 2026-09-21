import { NextResponse } from "next/server";
import { SITE_URL } from "@/lib/seo";

export const dynamic = "force-dynamic";

const BASE_URL = SITE_URL;

const PING_URLS = (sitemap: string) => [
  `https://www.google.com/ping?sitemap=${encodeURIComponent(sitemap)}`,
  `https://www.bing.com/ping?sitemap=${encodeURIComponent(sitemap)}`,
];

async function ping() {
  const sitemapUrl = `${BASE_URL}/sitemap.xml`;
  const results = await Promise.allSettled(
    PING_URLS(sitemapUrl).map((url) =>
      fetch(url, {
        method: "GET",
        cache: "no-store",
        signal: AbortSignal.timeout(15000),
      }),
    ),
  );

  return results.map((result, index) => ({
    engine: index === 0 ? "google" : "bing",
    status: result.status === "fulfilled" ? result.value.status : "failed",
  }));
}

export async function GET() {
  return NextResponse.json({ ok: true, sitemapUrl: `${BASE_URL}/sitemap.xml`, pings: await ping() });
}

export async function POST() {
  return NextResponse.json({ ok: true, sitemapUrl: `${BASE_URL}/sitemap.xml`, pings: await ping() });
}
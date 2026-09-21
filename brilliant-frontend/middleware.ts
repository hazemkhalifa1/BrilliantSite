import { NextRequest, NextResponse } from "next/server";
import createMiddleware from "next-intl/middleware";
import { routing } from "./src/i18n/routing";
import { SITE_URL } from "./lib/seo";

const intl = createMiddleware(routing);

export default function middleware(request: NextRequest) {
  // Canonical-host enforcement: 301 any domain variant (e.g. non-www or a
  // stale host) to the preferred www.brilliant-eng.com so link equity is not
  // split across duplicate hostnames.
  const host = (request.headers.get("host") ?? "").split(":")[0].toLowerCase();
  const canonicalHost = new URL(SITE_URL).host.toLowerCase();
  const domain = canonicalHost.replace(/^www\./, "");
  if (host.endsWith(domain) && host !== canonicalHost) {
    const url = new URL(request.url);
    url.host = canonicalHost;
    return NextResponse.redirect(url, 301);
  }
  return intl(request);
}

export const config = {
  matcher: "/((?!api|trpc|_next|_vercel|.*\\..*).*)",
};
import type { MetadataRoute } from "next";
import { apiFetch } from "@/lib/api";
import { SITE_URL, canonicalSlug } from "@/lib/seo";
import type { BlogPost, PagedResult, Product, Project } from "@/types";

const LOCALES = ["en", "ar"] as const;

const STATIC_ROUTES = [
  "",
  "/about",
  "/services",
  "/projects",
  "/products",
  "/blog",
  "/team",
  "/clients",
  "/contact",
];

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const lastModified = new Date();

  const entries: MetadataRoute.Sitemap = LOCALES.flatMap((locale) =>
    STATIC_ROUTES.map((route) => ({
      url: `${SITE_URL}/${locale}${route}`,
      lastModified,
      changeFrequency: "weekly",
      priority: route === "" ? 1 : 0.7,
    })),
  );

  const fetchSection = async <T,>(
    endpoint: string,
    mapper: (item: T) => { path: string; lastModified?: Date },
  ): Promise<{ path: string; lastModified?: Date }[]> =>
    apiFetch<PagedResult<T>>(endpoint)
      .then((data) => data.items.map(mapper))
      .catch(() => [] as { path: string; lastModified?: Date }[]);

  const [projectUrls, productUrls, rawPostUrls] = await Promise.all([
    fetchSection<Project>("/projects?onlyActive=true&pageSize=100", (p) => ({
      path: `/projects/${p.id}`,
      lastModified: p.createdAt ? new Date(p.createdAt) : undefined,
    })),
    fetchSection<Product>("/products?onlyActive=true&pageSize=100", (p) => ({
      path: `/products/${p.id}`,
      lastModified: p.createdAt ? new Date(p.createdAt) : undefined,
    })),
    fetchSection<BlogPost>("/blog?publishedOnly=true&pageSize=100", (p) => ({
      path: `/blog/${canonicalSlug(p.slug)}`,
      lastModified: p.publishedAt ? new Date(p.publishedAt) : undefined,
    })),
  ]);

  // De-duplicate blog posts that resolve to the same canonical slug, so the
  // sitemap never advertises legacy "…-2/…-3" URLs (which now 301).
  const seenPostPaths = new Set<string>();
  const postUrls = rawPostUrls.filter((u) => {
    if (seenPostPaths.has(u.path)) return false;
    seenPostPaths.add(u.path);
    return true;
  });

  const dynamicRoutes: MetadataRoute.Sitemap = [
    ...projectUrls,
    ...productUrls,
    ...postUrls,
  ].flatMap(({ path, lastModified: itemModified }) =>
    LOCALES.map((locale) => ({
      url: `${SITE_URL}/${locale}${path}`,
      lastModified: itemModified ?? lastModified,
      changeFrequency: "monthly" as const,
      priority: 0.5,
    })),
  );

  return [...entries, ...dynamicRoutes];
}
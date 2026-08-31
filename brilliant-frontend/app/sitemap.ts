import type { MetadataRoute } from "next";
import { apiFetch } from "@/lib/api";
import type { BlogPost, PagedResult, Product, Project } from "@/types";

const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3001";

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
      url: `${BASE_URL}/${locale}${route}`,
      lastModified,
      changeFrequency: "weekly",
      priority: route === "" ? 1 : 0.7,
    })),
  );

  const fetchSection = async <T,>(
    endpoint: string,
    mapper: (item: T) => string,
  ): Promise<string[]> =>
    apiFetch<PagedResult<T>>(endpoint)
      .then((data) => data.items.map(mapper))
      .catch(() => [] as string[]);

  const [projectUrls, productUrls, postUrls] = await Promise.all([
    fetchSection<Project>("/projects?onlyActive=true&pageSize=100", (p) => `/projects/${p.id}`),
    fetchSection<Product>("/products?onlyActive=true&pageSize=100", (p) => `/products/${p.id}`),
    fetchSection<BlogPost>("/blog?publishedOnly=true&pageSize=100", (p) => `/blog/${p.slug}`),
  ]);

  const dynamicRoutes = [...projectUrls, ...productUrls, ...postUrls].flatMap((route) =>
    LOCALES.map((locale) => ({
      url: `${BASE_URL}/${locale}${route}`,
      lastModified,
      changeFrequency: "monthly" as const,
      priority: 0.5,
    })),
  );

  return [...entries, ...dynamicRoutes];
}

import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { localeAlternates } from "@/lib/seo";
import { apiFetch } from "@/lib/api";
import { localized } from "@/lib/localize";
import type { PagedResult, Project, ProjectType } from "@/types";
import { cn } from "@/lib/utils";
import { ProjectCard } from "@/components/public/ProjectCard";
import { Reveal } from "@/components/public/Reveal";
import { Parallax } from "@/components/public/Parallax";
import { ArrowLeft, ArrowRight } from "lucide-react";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const t = await getTranslations({ locale, namespace: "projects" });
  return {
    title: t("metadataTitle"),
    description: t("metadataDescription"),
    alternates: localeAlternates(locale, "/projects"),
  };
}

const PAGE_SIZE = 9;

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80";

const heroGridStyle = {
  backgroundImage:
    "linear-gradient(to right, rgba(255,255,255,0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.07) 1px, transparent 1px)",
  backgroundSize: "32px 32px",
} as const;

async function getProjectsData() {
  const types = await apiFetch<PagedResult<ProjectType>>("/project-types?onlyActive=true&pageSize=50")
    .then((data) => data.items)
    .catch(() => [] as ProjectType[]);
  return { types };
}

interface ProjectsPageProps {
  searchParams: { typeId?: string; page?: string };
}

export default async function ProjectsPage({ searchParams }: ProjectsPageProps) {
  const locale = await getLocale();
  const t = await getTranslations("projects");
  const { types } = await getProjectsData();

  const rawTypeId = searchParams.typeId;
  const typeId = rawTypeId && !Number.isNaN(Number(rawTypeId)) ? Number(rawTypeId) : null;
  const rawPage = searchParams.page;
  const pageIndex = rawPage && !Number.isNaN(Number(rawPage)) ? Math.max(1, Number(rawPage)) : 1;

  const query = new URLSearchParams({
    onlyActive: "true",
    pageSize: String(PAGE_SIZE),
    pageIndex: String(pageIndex),
  });
  if (typeId) query.set("typeId", String(typeId));

  const result = await apiFetch<PagedResult<Project>>(`/projects?${query.toString()}`)
    .catch(() => ({ items: [] as Project[], totalCount: 0, pageIndex: 1, pageSize: PAGE_SIZE }));

  const totalPages = Math.max(1, Math.ceil(result.totalCount / PAGE_SIZE));
  const makeUrl = (type: number | null, page: number) => {
    const params = new URLSearchParams();
    if (type !== null) params.set("typeId", String(type));
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `/projects?${qs}` : "/projects";
  };

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
        <div className="container-brilliant">
<Reveal>
            <div className="flex flex-wrap gap-2">
              <Link
                href="/projects"
                className={cn(
                  "border px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors hover:-translate-y-0.5",
                  !typeId ? "border-primary bg-primary text-white" : "border-line text-neutral hover:bg-neutral-light",
                )}
              >
                {t("allProjects")}
              </Link>
              {types.map((type) => (
                <Link
                  key={type.id}
                  href={makeUrl(type.id, 1)}
                  className={cn(
                    "border px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-transform hover:-translate-y-0.5",
                    typeId === type.id
                      ? "border-primary bg-primary text-white"
                      : "border-line text-neutral hover:bg-neutral-light",
                  )}
                >
                  {localized(locale, type.name, type.nameAr)}
                </Link>
              ))}
            </div>
          </Reveal>

          {result.items.length > 0 ? (
            <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {result.items.map((project, index) => (
                <Reveal
                  key={project.id}
                  delay={index * 80}
                  from={index % 3 === 0 ? "left" : index % 3 === 2 ? "right" : "up"}
                >
                  <div className="pop-card h-full" style={{ transitionDelay: `${index * 80 + 120}ms` }}>
                    <ProjectCard project={project} />
                  </div>
                </Reveal>
              ))}
            </div>
          ) : (
            <p className="mt-12 text-center text-neutral/60">{t("noProjects")}</p>
          )}

          {totalPages > 1 && (
            <Reveal delay={120}>
              <div className="mt-12 flex items-center justify-center gap-2">
                <Link
                  href={makeUrl(typeId, pageIndex - 1)}
                  className={cn(
                    "group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                    pageIndex <= 1 && "pointer-events-none opacity-40",
                  )}
                >
                  <ArrowLeft className="mr-2 inline h-4 w-4 transition-transform duration-300 group-hover:-translate-x-1" />
                  {t("previous")}
                </Link>
                <span className="px-3 text-sm text-neutral/60">
                  {t("pageOf", { current: pageIndex, total: totalPages })}
                </span>
                <Link
                  href={makeUrl(typeId, pageIndex + 1)}
                  className={cn(
                    "group border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                    pageIndex >= totalPages && "pointer-events-none opacity-40",
                  )}
                >
                  {t("next")}
                  <ArrowRight className="ml-2 inline h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
                </Link>
              </div>
            </Reveal>
          )}
        </div>
      </section>
    </div>
  );
}

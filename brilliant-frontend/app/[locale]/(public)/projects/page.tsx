import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { apiFetch } from "@/lib/api";
import { localized } from "@/lib/localize";
import type { PagedResult, Project, ProjectType } from "@/types";
import { cn } from "@/lib/utils";
import { PageHeader } from "@/components/public/PageHeader";
import { ProjectCard } from "@/components/public/ProjectCard";

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
  };
}

const PAGE_SIZE = 9;

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
      <PageHeader
        eyebrow={t("eyebrow")}
        title={t("title")}
        subtitle={t("subtitle")}
        backgroundImage="https://images.unsplash.com/photo-1517581177682-a085bb7ffb15?auto=format&fit=crop&w=1920&q=80"
      />

      <section className="py-16 md:py-20">
        <div className="container-brilliant">
          <div className="flex flex-wrap gap-2">
            <Link
              href="/projects"
              className={cn(
                "border px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors",
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
                  "border px-5 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors",
                  typeId === type.id
                    ? "border-primary bg-primary text-white"
                    : "border-line text-neutral hover:bg-neutral-light",
                )}
              >
                {localized(locale, type.name, type.nameAr)}
              </Link>
            ))}
          </div>

          {result.items.length > 0 ? (
            <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {result.items.map((project) => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>
          ) : (
            <p className="mt-12 text-center text-neutral/60">{t("noProjects")}</p>
          )}

          {totalPages > 1 && (
            <div className="mt-12 flex items-center justify-center gap-2">
              <Link
                href={makeUrl(typeId, pageIndex - 1)}
                className={cn(
                  "border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                  pageIndex <= 1 && "pointer-events-none opacity-40",
                )}
              >
                {t("previous")}
              </Link>
              <span className="px-3 text-sm text-neutral/60">
                {t("pageOf", { current: pageIndex, total: totalPages })}
              </span>
              <Link
                href={makeUrl(typeId, pageIndex + 1)}
                className={cn(
                  "border border-line px-5 py-2.5 font-headline text-sm font-semibold uppercase transition-colors hover:bg-neutral-light",
                  pageIndex >= totalPages && "pointer-events-none opacity-40",
                )}
              >
                {t("next")}
              </Link>
            </div>
          )}
        </div>
      </section>
    </div>
  );
}
